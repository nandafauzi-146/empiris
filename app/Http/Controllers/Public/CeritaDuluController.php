<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Ai\ItineraryGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CeritaDuluController extends Controller
{
    public function __invoke(Request $request, ItineraryGenerator $generator): JsonResponse
    {
        $validated = $request->validate([
            'cerita' => ['required', 'string', 'min:10', 'max:1500'],
            'tipe_klien' => ['nullable', 'string', Rule::in(['korporat', 'kampus', 'instansi', 'umum'])],
            'durasi_hari' => ['nullable', 'integer', 'min:1', 'max:14'],
        ], [
            'cerita.required' => 'Ceritakan rencana atau kebutuhan kegiatan Anda terlebih dahulu.',
            'cerita.min' => 'Cerita minimal 10 karakter agar AI dapat memahami kebutuhan Anda.',
            'cerita.max' => 'Cerita maksimal 1500 karakter.',
            'tipe_klien.in' => 'Tipe klien tidak valid.',
            'durasi_hari.min' => 'Durasi minimal 1 hari.',
            'durasi_hari.max' => 'Durasi maksimal 14 hari.',
        ]);

        $result = $generator->generate(
            $validated['cerita'],
            $validated['tipe_klien'] ?? null,
            isset($validated['durasi_hari']) ? (int) $validated['durasi_hari'] : null
        );

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }
}
