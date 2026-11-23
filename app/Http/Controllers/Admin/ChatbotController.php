<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\ChatbotFaq;
use App\Models\ChatbotLog;
use App\Models\RecommendationLog;
use App\Models\User;
use App\Services\AdminAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'tab' => ['nullable', Rule::in(['faq', 'log', 'rekomendasi'])],
            'belum_terjawab' => ['nullable', 'boolean'],
        ], [
            'tab.in' => 'Tab chatbot tidak valid.',
        ]);
        $tab = $validated['tab'] ?? 'faq';

        return view('admin.chatbot.index', [
            'tab' => $tab,
            'faqs' => ChatbotFaq::query()->latest('id_faq')->paginate(10),
            'logs' => ChatbotLog::query()
                ->when(isset($validated['belum_terjawab']) && $validated['belum_terjawab'], fn ($query) => $query->where('terjawab', false))
                ->latest('created_at')
                ->paginate(20, ['*'], 'logs_page')
                ->withQueryString(),
            'recommendations' => RecommendationLog::query()->latest('created_at')->limit(20)->get(),
            'settings' => [
                'ambang_skor' => BusinessSetting::query()
                    ->where('kunci', 'ambang_skor_rekomendasi')
                    ->value('nilai') ?? '0.15',
                'jumlah_hasil' => BusinessSetting::query()
                    ->where('kunci', 'jumlah_hasil_rekomendasi')
                    ->value('nilai') ?? '5',
            ],
        ]);
    }

    public function storeFaq(Request $request, AdminAuditLogger $auditLogger): RedirectResponse
    {
        $validated = $this->validatedFaq($request);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        $faq = DB::transaction(function () use ($admin, $auditLogger, $validated): ChatbotFaq {
            $faq = ChatbotFaq::query()->create($validated);
            $auditLogger->record(
                $admin,
                'faq_chatbot_dibuat',
                'faq_chatbot',
                (int) $faq->id_faq,
                null,
                ['pertanyaan' => $faq->pertanyaan, 'aktif' => $faq->aktif],
            );

            return $faq;
        });

        return to_route('admin.chatbot.index', ['tab' => 'faq'])->with('status', 'FAQ chatbot berhasil ditambahkan.');
    }

    public function updateFaq(
        Request $request,
        ChatbotFaq $faq,
        AdminAuditLogger $auditLogger,
    ): RedirectResponse {
        $validated = $this->validatedFaq($request);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        DB::transaction(function () use ($admin, $auditLogger, $faq, $validated): void {
            $before = ['pertanyaan' => $faq->pertanyaan, 'aktif' => $faq->aktif];
            $faq->update($validated);
            $auditLogger->record(
                $admin,
                'faq_chatbot_diubah',
                'faq_chatbot',
                (int) $faq->id_faq,
                $before,
                ['pertanyaan' => $faq->pertanyaan, 'aktif' => $faq->aktif],
            );
        });

        return to_route('admin.chatbot.index', ['tab' => 'faq'])->with('status', 'FAQ chatbot berhasil diperbarui.');
    }

    public function destroyFaq(
        Request $request,
        ChatbotFaq $faq,
        AdminAuditLogger $auditLogger,
    ): RedirectResponse {
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        DB::transaction(function () use ($admin, $auditLogger, $faq): void {
            $previousActive = $faq->aktif;
            $faq->update(['aktif' => false]);
            $auditLogger->record(
                $admin,
                'faq_chatbot_dinonaktifkan',
                'faq_chatbot',
                (int) $faq->id_faq,
                ['aktif' => $previousActive],
                ['aktif' => false],
            );
        });

        return to_route('admin.chatbot.index', ['tab' => 'faq'])->with('status', 'FAQ dinonaktifkan.');
    }

    public function updateSettings(Request $request, AdminAuditLogger $auditLogger): RedirectResponse
    {
        $validated = $request->validate([
            'ambang_skor' => ['required', 'numeric', 'min:0', 'max:1'],
            'jumlah_hasil' => ['required', 'integer', 'min:1', 'max:20'],
        ], [
            'ambang_skor.required' => 'Ambang skor wajib diisi.',
            'jumlah_hasil.required' => 'Jumlah hasil wajib diisi.',
        ]);
        $admin = $request->user();
        abort_unless($admin instanceof User, 403);
        DB::transaction(function () use ($admin, $auditLogger, $validated): void {
            $values = [
                'ambang_skor_rekomendasi' => (string) $validated['ambang_skor'],
                'jumlah_hasil_rekomendasi' => (string) $validated['jumlah_hasil'],
            ];
            $before = [];

            foreach ($values as $key => $value) {
                $setting = BusinessSetting::query()->firstOrNew(['kunci' => $key]);
                $before[$key] = $setting->exists ? $setting->nilai : null;
                $setting->nilai = $value;
                $setting->save();
            }

            $auditLogger->record(
                $admin,
                'pengaturan_rekomendasi_diubah',
                'pengaturan_bisnis',
                null,
                $before,
                $values,
            );
        });

        return to_route('admin.chatbot.index', ['tab' => 'rekomendasi'])
            ->with('status', 'Pengaturan rekomendasi berhasil disimpan.');
    }

    /**
     * @return array{kata_kunci: list<string>, pertanyaan: string, jawaban: string, aktif: bool}
     */
    private function validatedFaq(Request $request): array
    {
        $validated = $request->validate([
            'kata_kunci' => ['required', 'string', 'max:500'],
            'pertanyaan' => ['required', 'string', 'max:500'],
            'jawaban' => ['required', 'string', 'max:5000'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'kata_kunci.required' => 'Kata kunci wajib diisi.',
            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'jawaban.required' => 'Jawaban wajib diisi.',
        ]);

        return [
            'kata_kunci' => array_values(array_filter(array_map('trim', explode(',', $validated['kata_kunci'])))),
            'pertanyaan' => $validated['pertanyaan'],
            'jawaban' => $validated['jawaban'],
            'aktif' => (bool) ($validated['aktif'] ?? false),
        ];
    }
}
