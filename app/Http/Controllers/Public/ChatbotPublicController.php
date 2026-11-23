<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotPublicController extends Controller
{
    public function __invoke(Request $request, ChatbotService $chatbot): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required', 'string', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:500'],
        ], [
            'message.required' => 'Pesan wajib diisi.',
            'message.max' => 'Pesan maksimal 500 karakter.',
        ]);

        // R1: id_klien dari Auth::user() jika login, bukan dari input
        $clientId = $request->user()?->getAuthIdentifier();

        $result = $chatbot->chat(
            userMessage: $validated['message'],
            history: $validated['history'] ?? [],
            clientId: is_int($clientId) ? $clientId : null,
        );

        return response()->json([
            'reply' => $result['reply'],
            'answered' => $result['answered'],
        ]);
    }
}
