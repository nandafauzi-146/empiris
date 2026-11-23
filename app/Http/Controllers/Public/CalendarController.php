<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\CalendarAvailability;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __invoke(Request $request, CalendarAvailability $availability): View
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
        $month = $month->startOfMonth();

        return view('pages.calendar', [
            'month' => $month,
            'statuses' => $availability->monthStatuses($month),
            'days' => $month->daysInMonth,
            'offset' => $month->dayOfWeekIso - 1,
            'previousMonth' => $month->subMonth()->format('Y-m'),
            'nextMonth' => $month->addMonth()->format('Y-m'),
        ]);
    }
}
