<?php

namespace App\Services;

use App\Models\CapacityMarker;
use App\Models\Project;
use Carbon\CarbonImmutable;

class CalendarAvailability
{
    /**
     * @return array<string, 'tersedia'|'terbatas'|'penuh'>
     */
    public function monthStatuses(CarbonImmutable $month): array
    {
        $start = $month->startOfMonth();
        $end = $month->endOfMonth();
        $markers = CapacityMarker::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->get(['tanggal', 'jenis'])
            ->groupBy(fn (CapacityMarker $marker): string => $marker->tanggal->toDateString());
        $projects = Project::query()
            ->where('status', '!=', 'batal')
            ->whereDate('tgl_mulai', '<=', $end->toDateString())
            ->whereDate('tgl_selesai', '>=', $start->toDateString())
            ->get(['tgl_mulai', 'tgl_selesai']);

        $statuses = [];
        for ($date = $start; $date <= $end; $date = $date->addDay()) {
            $key = $date->toDateString();
            $statuses[$key] = isset($markers[$key])
                ? 'penuh'
                : ($projects->contains(
                    fn (Project $project): bool => $project->tgl_mulai->toDateString() <= $key
                        && $project->tgl_selesai->toDateString() >= $key,
                ) ? 'terbatas' : 'tersedia');
        }

        return $statuses;
    }
}
