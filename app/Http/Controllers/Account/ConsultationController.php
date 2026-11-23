<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Enums\KonsultasiStatus;
use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\PaymentTerm;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function index(Request $request): View
    {
        /** @var LengthAwarePaginator<int, Consultation> $consultations */
        $consultations = $request->user()
            ->consultations()
            ->latest('created_at')
            ->latest('id_konsultasi')
            ->paginate(10);

        return view('account.consultations.index', [
            'consultations' => $consultations,
            'activeConsultationCount' => $request->user()
                ->consultations()
                ->whereIn('status', [
                    KonsultasiStatus::Baru->value,
                    KonsultasiStatus::Dibalas->value,
                    KonsultasiStatus::Penawaran->value,
                ])
                ->count(),
            'activeProjectCount' => Project::query()
                ->where('id_klien', $request->user()->getKey())
                ->whereIn('status', ['terjadwal', 'berjalan'])
                ->count(),
            'outstandingPaymentCount' => PaymentTerm::query()
                ->whereHas('project', fn ($query) => $query->where('id_klien', $request->user()->getKey()))
                ->where('status', 'belum_bayar')
                ->count(),
        ]);
    }

    public function show(Request $request, Consultation $consultation): View
    {
        $ownedConsultation = $request->user()
            ->consultations()
            ->findOrFail($consultation->getKey());

        return view('account.consultations.show', [
            'consultation' => $ownedConsultation,
            'replies' => $ownedConsultation->replies()->with('sender')->paginate(20),
            'offers' => $ownedConsultation->offers()->with('details')->get(),
        ]);
    }

    public function cancel(Request $request, Consultation $consultation): RedirectResponse
    {
        $client = $request->user();
        $ownedConsultation = $client->consultations()->findOrFail($consultation->getKey());

        DB::transaction(function () use ($client, $ownedConsultation): void {
            $lockedConsultation = Consultation::query()
                ->lockForUpdate()
                ->findOrFail($ownedConsultation->getKey());

            abort_unless($client->can('batal', $lockedConsultation), 403);

            $lockedConsultation->update([
                'status' => KonsultasiStatus::Batal,
            ]);
        });

        return to_route('account.consultations.show', $ownedConsultation)
            ->with('status', 'Konsultasi berhasil dibatalkan.');
    }
}
