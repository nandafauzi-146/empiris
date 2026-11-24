<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Enums\KonsultasiStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationRequest;
use App\Jobs\SendConsultationNotification;
use App\Models\Consultation;
use App\Models\EmailLog;
use App\Models\PaymentTerm;
use App\Models\Penawaran;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function create(Request $request): View
    {
        return view('account.consultations.create', [
            'selectedService' => $request->query('layanan') === 'Rental Armada'
                ? 'Rental Armada'
                : '',
        ]);
    }

    public function store(StoreConsultationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $adminEmail = config('services.empirise.admin_email');
        $user = $request->user();

        $consultation = DB::transaction(function () use ($user, $validated, $adminEmail): Consultation {
            $consultation = Consultation::create([
                ...$validated,
                // R1: id_klien selalu dari Auth::user(), tidak pernah dari input
                'id_klien' => $user->getAuthIdentifier(),
                'nomor_konsultasi' => 'KON-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'status' => KonsultasiStatus::Baru,
            ]);

            $consultation->emailLogs()->createMany([
                [
                    'jenis' => 'konfirmasi_klien',
                    'ke_email' => $consultation->email,
                    'subjek' => 'Konsultasi Anda sudah kami terima',
                ],
                [
                    'jenis' => 'notifikasi_admin',
                    'ke_email' => $adminEmail,
                    'subjek' => 'Konsultasi baru dari '.$consultation->nama,
                ],
            ]);

            return $consultation;
        });

        $consultation->emailLogs()->each(function (EmailLog $emailLog): void {
            SendConsultationNotification::dispatch($emailLog->getKey());
        });

        return to_route('account.consultations.show', $consultation)
            ->with('status', 'Konsultasi berhasil dikirim. Tim kami akan meninjau dan menghubungi Anda melalui email.');
    }

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
            'offers' => $ownedConsultation->offers()->with(['details', 'days', 'photos'])->get(),
        ]);
    }

    public function downloadOfferPdf(Request $request, Penawaran $offer): Response
    {
        $ownedOffer = Penawaran::query()
            ->whereHas('consultation', fn ($query) => $query->where('id_klien', $request->user()->getKey()))
            ->findOrFail($offer->getKey());

        $ownedOffer->load(['consultation', 'details', 'days', 'photos']);
        $pdf = Pdf::loadView('pdf.proposal', ['offer' => $ownedOffer]);
        $filename = 'Proposal-'.Str::slug($ownedOffer->judul ?: 'empirise').'-v'.$ownedOffer->versi.'.pdf';

        return $pdf->download($filename);
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
