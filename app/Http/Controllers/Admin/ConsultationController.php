<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KonsultasiStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationReplyRequest;
use App\Jobs\SendConsultationReplyNotification;
use App\Models\Consultation;
use App\Models\Penawaran;
use App\Services\PenawaranService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'catatan_admin' => ['nullable', 'string', 'max:5000'],
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
        );

        return to_route('admin.consultations.show', $consultation)
            ->with('status', 'Draf penawaran versi '.$offer->versi.' berhasil dibuat.');
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
