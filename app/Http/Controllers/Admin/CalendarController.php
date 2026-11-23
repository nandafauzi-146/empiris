<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CapacityMarker;
use App\Models\Project;
use App\Models\User;
use App\Services\AdminAuditLogger;
use App\Services\CalendarAvailability;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request, CalendarAvailability $availability): View
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ], [
            'month.date_format' => 'Format bulan harus YYYY-MM.',
        ]);
        $month = isset($validated['month'])
            ? CarbonImmutable::createFromFormat('!Y-m', $validated['month'])
            : CarbonImmutable::now()->startOfMonth();
        $month ??= CarbonImmutable::now()->startOfMonth();
        $start = $month->startOfMonth();
        $end = $month->endOfMonth();

        return view('admin.calendar.index', [
            'month' => $start,
            'statuses' => $availability->monthStatuses($start),
            'projects' => Project::query()
                ->with('client:id,name')
                ->whereDate('tgl_mulai', '<=', $end->toDateString())
                ->whereDate('tgl_selesai', '>=', $start->toDateString())
                ->whereNotIn('status', ['batal', 'selesai'])
                ->orderBy('tgl_mulai')
                ->get(),
            'markers' => CapacityMarker::query()
                ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
                ->get()
                ->groupBy(fn (CapacityMarker $marker): string => $marker->tanggal->toDateString()),
            'days' => $start->daysInMonth,
            'offset' => $start->dayOfWeekIso - 1,
        ]);
    }

    public function toggleMarker(
        Request $request,
        CalendarAvailability $availability,
        AdminAuditLogger $auditLogger,
    ): RedirectResponse {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', Rule::in(['tim', 'armada'])],
            'month' => ['required', 'date_format:Y-m'],
        ], [
            'tanggal.required' => 'Tanggal penanda wajib diisi.',
            'jenis.in' => 'Jenis penanda tidak valid.',
        ]);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        $before = null;
        $after = null;

        DB::transaction(function () use ($validated, $admin, &$before, &$after): void {
            $marker = CapacityMarker::query()
                ->whereDate('tanggal', $validated['tanggal'])
                ->where('jenis', $validated['jenis'])
                ->lockForUpdate()
                ->first();

            if ($marker) {
                $before = ['tanggal' => $marker->tanggal->toDateString(), 'jenis' => $marker->jenis];
                $marker->delete();
            } else {
                CapacityMarker::query()->create([
                    'tanggal' => $validated['tanggal'],
                    'jenis' => $validated['jenis'],
                    'dibuat_oleh' => $admin->id,
                ]);
                $after = ['tanggal' => $validated['tanggal'], 'jenis' => $validated['jenis']];
            }
        });
        $auditLogger->record(
            $admin,
            $before === null ? 'penanda_kapasitas_ditambahkan' : 'penanda_kapasitas_dihapus',
            'penanda_kapasitas',
            null,
            $before,
            $after,
        );

        return to_route('admin.calendar.index', ['month' => $validated['month']])
            ->with('status', 'Penanda kapasitas berhasil diperbarui.');
    }
}
