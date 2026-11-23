<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    /**
     * Model Gemini yang digunakan (dengan fallback otomatis jika model sibuk).
     */
    private const GEMINI_MODELS = [
        'gemini-flash-lite-latest',
        'gemini-2.5-flash-lite',
    ];

    /**
     * Prompt sistem formal dan profesional berisi seluruh pengetahuan website Empirise Jalan-Jalan.
     * Sesuai KETENTUAN_AI.md dan aturan R2 (tanpa akses database).
     */
    private function systemInstruction(): string
    {
        return <<<'PROMPT'
Kamu adalah Asisten AI resmi dari biro perjalanan dan penyelenggara kegiatan Empirise Jalan-Jalan.
Tugas utama Anda adalah memberikan informasi secara profesional, formal, santun, dan terstruktur rapi menggunakan bahasa Indonesia baku (PUEBI/EYD) kepada pengunjung website.

Pedoman Komunikasi Formal & Profesional:
- Selalu bersikap sebagai "Asisten AI Empirise Jalan-Jalan" yang berwibawa, tepercaya, dan santun.
- Sapa pengunjung dengan panggilan terhormat "Bapak/Ibu" atau "Anda" (dengan huruf kapital).
- DILARANG KERAS menggunakan kata-kata santai, gaul, atau tidak resmi (seperti: "Yuk", "seru", "keseruan", "banget", "nih", "dong", "gaes", "asik").
- Susun jawaban secara rapi, elegan, dan padat (maksimal 2 hingga 3 paragraf singkat atau gunakan poin-poin terstruktur yang rapi).
- Selalu akhiri dengan kalimat tindak lanjut yang formal dan mengarahkan pengunjung ke halaman konsultasi resmi (/konsultasi atau /konsultasi/baru).

Informasi Lengkap Layanan & Profil Empirise Jalan-Jalan:
- Profil Perusahaan: Biro perjalanan wisata dan penyelenggara kegiatan kustom tepercaya (tour, event, outbound, rental armada, team building).
- 5 Layanan Utama:
  1. Tour & Wisata: Perjalanan wisata kustom domestik dan internasional untuk keluarga, korporasi, maupun instansi.
  2. Event & Gathering: Pengelolaan kegiatan gathering perusahaan, perayaan hari jadi, rapat kerja, dan seminar dari perencanaan hingga pelaksanaan.
  3. Outbound: Program simulasi luar ruang dan aktivitas kebersamaan terarah bersama fasilitator tersertifikasi.
  4. Rental Armada: Layanan transportasi rombongan berizin resmi dengan armada Big Bus (45-59 seat), Medium Bus (30-35 seat), Minibus (Toyota HiAce & Isuzu Elf), dan armada MPV, didampingi pengemudi berpengalaman.
  5. Team Building: Program terintegrasi untuk memperkuat kolaborasi, komunikasi, dan produktivitas tim.
- Destinasi Unggulan: Yogyakarta, Solo, Dataran Tinggi Dieng, Semarang, Malang & Bromo, Banyuwangi, Bandung, Bali, Lombok, Labuan Bajo, dan rute kustom lainnya.
- Prosedur Pemesanan & Konsultasi:
  1. Klien menyampaikan rincian kebutuhan melalui formulir di halaman Konsultasi (/konsultasi).
  2. Tim admin meninjau kebutuhan dan menerbitkan surat penawaran resmi.
  3. Klien menyetujui penawaran (kesepakatan / deal).
  4. Pelaksanaan kegiatan dan penyelesaian pembayaran sesuai termin resmi yang disepakati.
- Jam Operasional Kantor: Senin sampai Sabtu, pukul 08.00 hingga 17.00 WIB.

Larangan Mutlak (Sesuai KETENTUAN_AI.md & Aturan Keras R2):
- LARANGAN HARGA: Jangan pernah menyebutkan nominal harga, estimasi angka rupiah, diskon, atau potongan biaya dalam bentuk apa pun. Jika ditanyakan mengenai biaya/harga, jelaskan secara formal bahwa anggaran dirancang secara kustom dan transparan melalui surat penawaran resmi sesuai rincian kebutuhan kegiatan.
- LARANGAN KOMITMEN KETERSEDIAAN: Jangan menjanjikan kepastian tanggal atau ketersediaan unit armada secara mandiri. Jelaskan bahwa tim admin akan memeriksa dan mengonfirmasi ketersediaan jadwal secara langsung setelah konsultasi diajukan.
- LARANGAN KLAIM MANUSIA: Jangan pernah mengaku sebagai staf manusia.
- LARANGAN DATA SENSITIF: Jangan meminta NIK, kata sandi, atau nomor rekening/kartu.
- LARANGAN TOPIK DI LUAR BISNIS: Fokuskan percakapan hanya pada lingkup layanan Empirise Jalan-Jalan.
PROMPT;
    }

    /**
     * Kirim pesan pengguna ke Google Gemini API.
     * Jawaban murni dari AI secara profesional dan formal, tanpa mengakses database.
     */
    public function chat(string $userMessage, array $history = []): array
    {
        // Validasi panjang pesan (R15: dibatasi maksimal 500 karakter)
        $userMessage = mb_substr(strip_tags($userMessage), 0, 500);

        // Susun riwayat percakapan untuk format Gemini API
        $contents = [];

        foreach (array_slice($history, -8) as $msg) {
            if (isset($msg['role'], $msg['content']) && in_array($msg['role'], ['user', 'assistant'], true)) {
                $contents[] = [
                    'role' => $msg['role'] === 'assistant' ? 'model' : 'user',
                    'parts' => [
                        ['text' => mb_substr((string) $msg['content'], 0, 500)],
                    ],
                ];
            }
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage],
            ],
        ];

        $apiKey = config('services.gemini.key');

        if (! empty($apiKey)) {
            foreach (self::GEMINI_MODELS as $model) {
                try {
                    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                    $response = Http::timeout(12)->post($url, [
                        'systemInstruction' => [
                            'parts' => [
                                ['text' => $this->systemInstruction()],
                            ],
                        ],
                        'contents' => $contents,
                        'generationConfig' => [
                            'maxOutputTokens' => 1200,
                            'temperature' => 0.4,
                        ],
                    ]);

                    if ($response->successful()) {
                        $aiText = (string) $response->json('candidates.0.content.parts.0.text', '');

                        if (! empty(trim($aiText))) {
                            // Validasi guardrail output — R2: AI dilarang menyebut harga
                            if ($this->containsPrice($aiText)) {
                                Log::warning('Gemini guardrail: response contains price pattern', ['snippet' => mb_substr($aiText, 0, 100)]);
                                $aiText = 'Mengenai anggaran biaya, Empirise Jalan-Jalan tidak menetapkan tarif kaku di awal karena seluruh kegiatan dirancang secara kustom. Tim kami akan menyusun surat penawaran resmi yang transparan dan efisien sesuai kebutuhan kegiatan Anda. Silakan menyampaikan rincian rencana Anda melalui formulir konsultasi.';
                            }

                            return [
                                'reply' => trim($aiText),
                                'answered' => true,
                            ];
                        }
                    } else {
                        Log::warning("Gemini model {$model} returned status {$response->status()}", [
                            'body' => mb_substr($response->body(), 0, 200),
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::warning("Gemini model {$model} exception: {$e->getMessage()}");
                }
            }
        }

        // Jawaban formal cadangan bila koneksi terputus total
        return [
            'reply' => 'Selamat datang di Empirise Jalan-Jalan. Saya Asisten AI resmi yang siap memberikan informasi terkait layanan Tour & Wisata, Event & Gathering, Outbound, Rental Armada, dan Team Building. Apakah ada rencana kegiatan yang ingin Anda konsultasikan bersama tim kami?',
            'answered' => true,
        ];
    }

    /**
     * Guardrail: periksa apakah output AI menyebut pola harga.
     */
    private function containsPrice(string $text): bool
    {
        $patterns = ['/Rp\s*[\d.,]+/i', '/\b\d+\s*(ribu|juta|rb|jt)\b/i', '/\bdiskon\b/i', '/\bpotongan harga\b/i'];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }
}
