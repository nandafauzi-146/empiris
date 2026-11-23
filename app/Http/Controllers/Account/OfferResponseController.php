<?php

namespace App\Http\Controllers\Account;

use App\Enums\PenawaranStatus;
use App\Http\Controllers\Controller;
use App\Models\Penawaran;
use App\Services\PenawaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class OfferResponseController extends Controller
{
    public function store(Request $request, Penawaran $offer, PenawaranService $service): JsonResponse|RedirectResponse
    {
        $ownedOffer = Penawaran::query()
            ->whereHas('consultation', fn ($query) => $query->where('id_klien', $request->user()->getKey()))
            ->findOrFail($offer->getKey());
        $offer = $ownedOffer;

        abort_unless($offer->status === PenawaranStatus::Dikirim, 409);

        Gate::authorize('tanggapi', $offer);

        $validated = $request->validate([
            'aksi' => ['required', Rule::in(['setuju', 'revisi', 'tolak'])],
            'catatan' => ['nullable', 'required_if:aksi,revisi', 'string', 'max:2000'],
        ], [
            'aksi.in' => 'Tindakan penawaran tidak valid.',
            'catatan.required_if' => 'Catatan wajib diisi saat meminta revisi.',
        ]);

        $project = match ($validated['aksi']) {
            'setuju' => $service->setujui($offer, $request->user()),
            'revisi' => (function () use ($service, $offer, $request, $validated): null {
                $service->mintaRevisi($offer, $request->user(), $validated['catatan']);

                return null;
            })(),
            'tolak' => (function () use ($service, $offer, $request, $validated): null {
                $service->tolak($offer, $request->user(), $validated['catatan'] ?? null);

                return null;
            })(),
        };

        $status = $validated['aksi'] === 'setuju'
            ? 'disetujui'
            : ($validated['aksi'] === 'revisi' ? 'revisi' : 'ditolak');

        if (! $request->expectsJson()) {
            $redirect = $project
                ? to_route('account.projects.index')
                : to_route('account.consultations.show', $offer->consultation);

            return $redirect->with(
                'status',
                $status === 'disetujui'
                    ? 'Penawaran disetujui. Proyek Anda berhasil dibuat.'
                    : ($status === 'revisi' ? 'Permintaan revisi berhasil dikirim.' : 'Penawaran berhasil ditolak.'),
            );
        }

        return response()->json([
            'status' => $validated['aksi'] === 'setuju' ? 'disetujui' : ($validated['aksi'] === 'revisi' ? 'revisi' : 'ditolak'),
            'id_proyek' => $project?->getKey(),
            'url' => $project ? route('account.projects.index') : route('account.consultations.show', $offer->consultation),
        ]);
    }
}
