<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KonsultasiStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationReplyRequest;
use App\Jobs\SendConsultationReplyNotification;
use App\Models\Consultation;
use App\Models\Penawaran;
use App\Models\PenawaranFoto;
use App\Services\PenawaranService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => [
                'nullable',
                'string',
                Rule::in(array_column(KonsultasiStatus::cases(), 'value')),
            ],
            'q' => ['nullable', 'string', 'max:120'],
        ], [
            'status.in' => 'Status konsultasi tidak valid.',
            'q.max' => 'Pencarian maksimal 120 karakter.',
        ]);
        $query = Consultation::query()
            ->withCount('replies')
            ->when(
                $validated['status'] ?? null,
                fn ($query, string $status) => $query->where('status', $status),
            )
            ->when(
                $request->boolean('perlu_ditanggapi'),
                fn ($query) => $query->needsAdminResponse(),
            )
            ->when($validated['q'] ?? null, function ($query, string $term): void {
                $query->where(function ($query) use ($term): void {
                    $query->where('nomor_konsultasi', 'like', "%{$term}%")
                        ->orWhere('nama', 'like', "%{$term}%")
                        ->orWhere('instansi', 'like', "%{$term}%");
                });
            });

        return view('admin.consultations.index', [
            'consultations' => $query
                ->orderByRaw("CASE WHEN status IN ('baru', 'dibalas') THEN 0 ELSE 1 END")
                ->latest('updated_at')
                ->latest('id_konsultasi')
                ->paginate(20)
                ->withQueryString(),
            'filters' => $validated,
            'needsResponseOnly' => $request->boolean('perlu_ditanggapi'),
        ]);
    }

    public function show(Consultation $consultation): View
    {
        return view('admin.consultations.show', [
            'consultation' => $consultation,
            'replies' => $consultation->replies()->with('sender')->paginate(20),
            'offers' => $consultation->offers()->with('details')->get(),
        ]);
    }

    public function storeOffer(
        Request $request,
        Consultation $consultation,
        PenawaranService $service,
    ): RedirectResponse {
        $validated = $request->validate([
            'judul' => ['nullable', 'string', 'max:190'],
            'pemahaman' => ['nullable', 'string', 'max:5000'],
            'catatan_admin' => ['nullable', 'string', 'max:5000'],
            'cakupan_termasuk' => ['nullable', 'string', 'max:5000'],
            'cakupan_tidak' => ['nullable', 'string', 'max:5000'],
            'keselamatan' => ['nullable', 'string', 'max:5000'],
            'syarat' => ['nullable', 'string', 'max:5000'],
            'berlaku_hari' => ['nullable', 'integer', 'min:1', 'max:365'],
            'days' => ['nullable', 'array'],
            'days.*.judul' => ['nullable', 'string', 'max:60'],
            'days.*.isi' => ['nullable', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.nama_item' => ['required', 'string', 'max:190'],
            'items.*.deskripsi' => ['nullable', 'string', 'max:2000'],
            'items.*.jumlah' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'items.*.satuan' => ['required', 'string', 'max:30'],
            'items.*.harga_satuan' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
        ], [
            'items.required' => 'Minimal satu komponen penawaran wajib diisi.',
            'items.*.nama_item.required' => 'Nama komponen wajib diisi.',
            'items.*.jumlah.required' => 'Jumlah komponen wajib diisi.',
            'items.*.satuan.required' => 'Satuan komponen wajib diisi.',
        ]);

        abort_unless($consultation->id_klien !== null, 422, 'Penawaran hanya dapat dibuat untuk konsultasi yang tertaut ke klien.');

        $offer = $service->buat(
            $consultation,
            collect($validated['items'])->map(fn (array $item): array => [
                'nama_item' => $item['nama_item'],
                'deskripsi' => $item['deskripsi'] ?? null,
                'jumlah' => $item['jumlah'],
                'satuan' => $item['satuan'],
                'harga_satuan' => $item['harga_satuan'] ?? null,
                'asal' => 'manual',
            ])->all(),
            $validated['catatan_admin'] ?? null,
            [
                'judul' => $validated['judul'] ?? null,
                'pemahaman' => $validated['pemahaman'] ?? null,
                'cakupan_termasuk' => $validated['cakupan_termasuk'] ?? null,
                'cakupan_tidak' => $validated['cakupan_tidak'] ?? null,
                'keselamatan' => $validated['keselamatan'] ?? null,
                'syarat' => $validated['syarat'] ?? null,
                'berlaku_hari' => isset($validated['berlaku_hari']) ? (int) $validated['berlaku_hari'] : null,
                'days' => $validated['days'] ?? [],
            ],
        );

        return to_route('admin.consultations.show', $consultation)
            ->with('status', 'Draf penawaran versi '.$offer->versi.' berhasil dibuat.');
    }

    public function newOfferVersion(
        Consultation $consultation,
        Penawaran $offer,
        PenawaranService $service,
    ): RedirectResponse {
        abort_unless($offer->id_konsultasi === $consultation->getKey(), 404);
        $newOffer = $service->buatVersiBaru($offer);

        return to_route('admin.consultations.show', $consultation)
            ->with('status', 'Draf penawaran versi '.$newOffer->versi.' berhasil dibuat dari versi sebelumnya.');
    }

    public function sendOffer(
        Consultation $consultation,
        Penawaran $offer,
        PenawaranService $service,
    ): RedirectResponse {
        abort_unless($offer->id_konsultasi === $consultation->getKey(), 404);
        $service->kirim($offer);

        return to_route('admin.consultations.show', $consultation)
            ->with('status', 'Penawaran berhasil dikirim kepada klien.');
    }

    public function downloadPdf(Consultation $consultation, Penawaran $offer): Response
    {
        abort_unless($offer->id_konsultasi === $consultation->getKey(), 404);
        $offer->load(['consultation', 'details', 'days', 'photos']);
        $pdf = Pdf::loadView('pdf.proposal', ['offer' => $offer]);
        $filename = 'Proposal-'.Str::slug($offer->judul ?: 'empirise').'-v'.$offer->versi.'.pdf';

        return $pdf->download($filename);
    }

    public function previewProposal(Consultation $consultation, Penawaran $offer): View
    {
        abort_unless($offer->id_konsultasi === $consultation->getKey(), 404);
        $offer->load(['consultation', 'details', 'days', 'photos']);

        return view('pdf.proposal', ['offer' => $offer]);
    }

    public function uploadLogo(Request $request, Consultation $consultation, Penawaran $offer): RedirectResponse
    {
        abort_unless($offer->id_konsultasi === $consultation->getKey(), 404);
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'logo.required' => 'Pilih berkas logo untuk diunggah.',
            'logo.image' => 'Berkas harus berupa gambar.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
        ]);

        $path = $request->file('logo')->store('proposals/logos', 'public');
        $offer->update(['logo_path' => $path]);

        return to_route('admin.consultations.show', $consultation)
            ->with('status', 'Logo klien berhasil diunggah untuk proposal ini.');
    }

    public function uploadPhoto(Request $request, Consultation $consultation, Penawaran $offer): RedirectResponse
    {
        abort_unless($offer->id_konsultasi === $consultation->getKey(), 404);
        $validated = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'jenis' => ['required', Rule::in(['sampul', 'destinasi', 'galeri'])],
            'nama_destinasi' => ['nullable', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:200'],
        ], [
            'foto.required' => 'Pilih berkas foto untuk diunggah.',
            'foto.max' => 'Ukuran foto maksimal 4MB.',
            'jenis.in' => 'Jenis foto tidak valid.',
        ]);

        $path = $request->file('foto')->store('proposals/photos', 'public');
        $urutan = $offer->photos()->count() + 1;

        $offer->photos()->create([
            'jenis' => $validated['jenis'],
            'nama_destinasi' => $validated['nama_destinasi'] ?? null,
            'keterangan' => $validated['keterangan'] ?? null,
            'path' => $path,
            'urutan' => $urutan,
        ]);

        return to_route('admin.consultations.show', $consultation)
            ->with('status', 'Foto kegiatan / destinasi berhasil ditambahkan ke proposal.');
    }

    public function deletePhoto(Consultation $consultation, Penawaran $offer, PenawaranFoto $photo): RedirectResponse
    {
        abort_unless($offer->id_konsultasi === $consultation->getKey(), 404);
        abort_unless($photo->id_penawaran === $offer->getKey(), 404);

        if (Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }
        $photo->delete();

        return to_route('admin.consultations.show', $consultation)
            ->with('status', 'Foto berhasil dihapus dari proposal.');
    }

    public function storeReply(
        StoreConsultationReplyRequest $request,
        Consultation $consultation,
    ): RedirectResponse {
        [$reply, $emailLog] = DB::transaction(function () use ($request, $consultation): array {
            $reply = $consultation->replies()->create([
                'id_pengirim' => $request->user()->getKey(),
                'pesan' => $request->validated('pesan'),
            ]);

            $consultation->update(['status' => KonsultasiStatus::Dibalas]);

            $emailLog = $consultation->emailLogs()->create([
                'jenis' => 'balasan_konsultasi',
                'ke_email' => $consultation->email,
                'subjek' => 'Balasan baru untuk konsultasi Anda',
            ]);

            return [$reply, $emailLog];
        });

        SendConsultationReplyNotification::dispatch($emailLog->getKey(), $reply->getKey());

        return to_route('admin.consultations.show', $consultation)
            ->with('status', 'Balasan tersimpan dan email notifikasi dijadwalkan.');
    }
}
