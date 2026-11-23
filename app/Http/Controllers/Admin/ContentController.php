<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminContent;
use App\Models\User;
use App\Services\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'jenis' => ['nullable', Rule::in(array_keys(AdminContent::TYPES))],
        ], [
            'jenis.in' => 'Kategori konten tidak valid.',
        ]);
        $type = (string) ($validated['jenis'] ?? 'layanan');

        return view('admin.content.index', [
            'type' => $type,
            'types' => AdminContent::TYPES,
            'typeLabel' => AdminContent::TYPES[$type],
            'typeLinks' => collect(AdminContent::TYPES)
                ->map(fn (string $label, string $key): object => (object) [
                    'label' => $label,
                    'url' => route('admin.content.index', ['jenis' => $key]),
                    'isCurrent' => $type === $key,
                    'className' => $type === $key
                        ? 'border-ink bg-ink text-white'
                        : 'border-line text-copy hover:border-ink',
                ])
                ->values(),
            'supportsImages' => in_array($type, ['destinasi', 'portofolio', 'layanan'], true),
            'contents' => AdminContent::query()
                ->where('jenis', $type)
                ->orderBy('urutan')
                ->orderBy('judul')
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function store(Request $request, AdminAuditLogger $auditLogger): RedirectResponse
    {
        $validated = $this->validatedContent($request);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        $content = DB::transaction(function () use ($validated, $admin, $auditLogger): AdminContent {
            $content = AdminContent::query()->create($validated);
            $auditLogger->record(
                $admin,
                'konten_dibuat',
                'konten_admin',
                (int) $content->id_konten,
                null,
                $content->toArray(),
            );

            return $content;
        });

        return to_route('admin.content.index', ['jenis' => $content->jenis])
            ->with('status', 'Konten berhasil ditambahkan.');
    }

    public function update(Request $request, AdminContent $content, AdminAuditLogger $auditLogger): RedirectResponse
    {
        $validated = $this->validatedContent($request, $content);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        DB::transaction(function () use ($admin, $auditLogger, $content, $validated): void {
            $before = $content->only(['jenis', 'judul', 'slug', 'deskripsi', 'metadata', 'aktif', 'urutan']);
            $content->update($validated);
            $auditLogger->record(
                $admin,
                'konten_diubah',
                'konten_admin',
                (int) $content->id_konten,
                $before,
                $content->only(array_keys($before)),
            );
        });

        return to_route('admin.content.index', ['jenis' => $content->jenis])
            ->with('status', 'Konten berhasil diperbarui.');
    }

    public function destroy(AdminContent $content, Request $request, AdminAuditLogger $auditLogger): RedirectResponse
    {
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        DB::transaction(function () use ($admin, $auditLogger, $content): void {
            $before = $content->only(['aktif', 'status']);
            $content->update(['aktif' => false, 'status' => 'tersembunyi']);
            $auditLogger->record(
                $admin,
                'konten_dinonaktifkan',
                'konten_admin',
                (int) $content->id_konten,
                $before,
                $content->only(['aktif', 'status']),
            );
        });

        return to_route('admin.content.index', ['jenis' => $content->jenis])
            ->with('status', 'Konten dinonaktifkan dan tidak dihapus permanen.');
    }

    /**
     * @return array{jenis: string, judul: string, slug: ?string, deskripsi: ?string, metadata: array<string, mixed>|null, status: string, aktif: bool, urutan: int}
     */
    private function validatedContent(Request $request, ?AdminContent $existing = null): array
    {
        $validated = $request->validate([
            'jenis' => ['required', Rule::in(array_keys(AdminContent::TYPES))],
            'judul' => ['required', 'string', 'max:190'],
            'slug' => [
                'nullable',
                'string',
                'max:190',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('konten_admin', 'slug')
                    ->where(fn ($query) => $query->where('jenis', $request->input('jenis')))
                    ->ignore($existing?->id_konten, 'id_konten'),
            ],
            'deskripsi' => ['nullable', 'string', 'max:10000'],
            'metadata_json' => ['nullable', 'string', 'json'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'icon' => ['nullable', 'string', 'in:map,calendar,gamepad,bus,users'],
            'status' => ['required', Rule::in(['tampil', 'draf', 'tersembunyi'])],
            'aktif' => ['nullable', 'boolean'],
            'urutan' => ['required', 'integer', 'min:0', 'max:100000'],
        ], [
            'jenis.in' => 'Kategori konten tidak valid.',
            'judul.required' => 'Judul wajib diisi.',
            'slug.regex' => 'Slug hanya boleh memakai huruf kecil, angka, dan tanda hubung.',
            'slug.unique' => 'Slug sudah digunakan pada kategori konten yang sama.',
            'status.in' => 'Status konten tidak valid.',
            'urutan.required' => 'Urutan wajib diisi.',
            'gambar.image' => 'Gambar harus berupa file gambar.',
            'gambar.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'gambar.max' => 'Ukuran gambar maksimal 4 MB.',
        ]);

        $metadata = $existing?->metadata ?? null;
        if (isset($validated['metadata_json'])) {
            $decodedMetadata = json_decode($validated['metadata_json'], true);
            if (! is_array($decodedMetadata) || array_is_list($decodedMetadata)) {
                throw ValidationException::withMessages([
                    'metadata_json' => 'Metadata harus berupa objek JSON.',
                ]);
            }
            $metadata = $decodedMetadata;
        }

        if ($request->hasFile('gambar')) {
            $storedPath = $request->file('gambar')->store('konten-web', 'public');
            if (! is_string($storedPath)) {
                throw ValidationException::withMessages([
                    'gambar' => 'Gambar gagal disimpan.',
                ]);
            }

            $metadata = is_array($metadata) ? $metadata : [];
            $metadata['image'] = 'storage/'.$storedPath;

            if ($existing !== null) {
                $oldImage = $existing->metadata['image'] ?? null;
                if (is_string($oldImage) && str_starts_with($oldImage, 'storage/konten-web/')) {
                    Storage::disk('public')->delete(Str::after($oldImage, 'storage/'));
                }
            }
        }

        if (isset($validated['icon']) && is_string($validated['icon'])) {
            $metadata = is_array($metadata) ? $metadata : [];
            $metadata['icon'] = $validated['icon'];
        }

        unset($validated['metadata_json']);
        unset($validated['gambar']);
        unset($validated['icon']);

        $slug = isset($validated['slug']) && is_string($validated['slug'])
            ? Str::slug($validated['slug'])
            : null;

        $limits = [
            'layanan' => 5,
            'portofolio' => 6,
            'klien' => 40,
            'testimoni' => 6,
            'faq' => 20,
            'tim' => 8,
            'destinasi' => 30,
        ];
        $limit = $limits[$validated['jenis']] ?? 20;

        if (($validated['status'] === 'tampil') && ($validated['aktif'] ?? false)) {
            $publishedCount = AdminContent::query()
                ->where('jenis', $validated['jenis'])
                ->where('aktif', true)
                ->where('status', 'tampil')
                ->when($existing !== null, fn ($query) => $query->where('id_konten', '!=', $existing->getKey()))
                ->count();

            if ($publishedCount >= $limit) {
                throw ValidationException::withMessages([
                    'status' => "Kategori {$validated['jenis']} maksimal menampilkan {$limit} konten agar tampilan tetap rapih.",
                ]);
            }
        }

        return [
            ...$validated,
            'slug' => $slug,
            'aktif' => (bool) ($validated['aktif'] ?? false),
            'metadata' => $metadata,
        ];
    }
}
