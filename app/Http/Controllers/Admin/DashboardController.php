<?php

namespace App\Http\Controllers\Admin;

use App\Enums\KonsultasiStatus;
use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\PaymentTerm;
use App\Models\Project;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'newConsultationCount' => Consultation::query()
                ->where('status', KonsultasiStatus::Baru->value)
                ->count(),
            'activeConsultationCount' => Consultation::query()
                ->whereIn('status', [
                    KonsultasiStatus::Baru->value,
                    KonsultasiStatus::Dibalas->value,
                    KonsultasiStatus::Penawaran->value,
                ])
                ->count(),
            'totalConsultationCount' => Consultation::query()->count(),
            'pendingOfferCount' => Consultation::query()
                ->where('status', KonsultasiStatus::Penawaran->value)
                ->count(),
            'needsResponseCount' => Consultation::needsAdminResponse()->count(),
            'runningProjectCount' => Project::query()->where('status', 'berjalan')->count(),
            'overduePaymentCount' => PaymentTerm::query()
                ->where('status', 'belum_bayar')
                ->whereDate('jatuh_tempo', '<', today())
                ->count(),
            'todayTasks' => Consultation::needsAdminResponse()
                ->oldest('created_at')
                ->oldest('id_konsultasi')
                ->limit(5)
                ->get(['id_konsultasi', 'nomor_konsultasi', 'nama', 'instansi']),
            'upcomingProjects' => Project::query()
                ->with('client:id,name')
                ->whereBetween('tgl_mulai', [today(), today()->addDays(7)])
                ->whereNotIn('status', ['batal', 'selesai'])
                ->orderBy('tgl_mulai')
                ->limit(5)
                ->get(),
            'recentConsultations' => Consultation::query()
                ->select([
                    'id_konsultasi',
                    'nomor_konsultasi',
                    'nama',
                    'layanan',
                    'status',
                    'updated_at',
                ])
                ->latest('updated_at')
                ->latest('id_konsultasi')
                ->limit(5)
                ->get(),
        ]);
    }
}
