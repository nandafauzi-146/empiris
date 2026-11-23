<?php

namespace App\Http\Controllers\Public;

use App\Enums\KonsultasiStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationRequest;
use App\Jobs\SendConsultationNotification;
use App\Models\Consultation;
use App\Models\EmailLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        // Klien yang sudah login dan terverifikasi → form konsultasi akun
        if ($request->user()?->role === UserRole::Klien && $request->user()->hasVerifiedEmail()) {
            return redirect()->route('account.consultations.create');
        }

        return view('pages.consultation.create', [
            'selectedService' => $request->query('layanan') === 'Rental Armada'
                ? 'Rental Armada'
                : '',
        ]);
    }

    public function store(StoreConsultationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $adminEmail = config('services.empirise.admin_email');

        $consultation = DB::transaction(function () use ($request, $validated, $adminEmail): Consultation {
            $consultation = Consultation::create([
                ...$validated,
                'id_klien' => $request->user()?->role === UserRole::Klien
                    ? $request->user()->getAuthIdentifier()
                    : null,
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

        return to_route('consultation.create')
            ->with([
                'consultation_number' => $consultation->nomor_konsultasi,
                'consultation_id' => $request->user()?->role === UserRole::Klien
                    && $request->user()->hasVerifiedEmail()
                        ? $consultation->getKey()
                        : null,
            ]);
    }
}
