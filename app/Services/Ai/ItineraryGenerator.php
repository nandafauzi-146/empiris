<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Models\AdminContent;
use App\Models\RecommendationLog;
use App\Services\PiiMasker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ItineraryGenerator
{
    private const GEMINI_MODELS = [
        'gemini-flash-lite-latest',
        'gemini-2.5-flash-lite',
    ];

    /**
     * Hasilkan rekomendasi dan itinerary berbasis cerita klien.
     * Mengimplementasikan CHG-01 (Cerita Dulu) dan CHG-07 (Destinasi Bebas).
     *
     * @return array<string, mixed>
     */
    public function generate(string $story, ?string $tipeKlien = null, ?int $durasiHari = null): array
    {
        $startTime = microtime(true);
        $cleanStory = mb_substr(strip_tags($story), 0, 1500);
        $maskedStory = PiiMasker::mask($cleanStory);

        // Ambil destinasi resmi dari database
        $databaseDestinations = AdminContent::query()
            ->where('jenis', 'destinasi')
            ->where('aktif', true)
            ->get(['id_konten', 'judul', 'deskripsi', 'metadata']);

        $destinationListStr = $databaseDestinations->map(
            fn ($d) => "ID: {$d->id_konten} | Nama: {$d->judul} | Wilayah: ".($d->metadata['wilayah'] ?? '')." | Deskripsi: {$d->deskripsi}"
        )->implode("\n");

        $prompt = <<<PROMPT
Anda adalah AI Konsultan Perjalanan resmi Empirise Jalan-Jalan.
Tugas Anda: Baca dan pahami cerita rencana kegiatan dari calon klien, kemudian rancang gambaran awal itinerary kustom.

Cerita Klien:
"{$maskedStory}"
Segmen Klien (jika ada): {$tipeKlien}
Rencana Durasi (jika ada): {$durasiHari} hari

Daftar Destinasi Resmi di Database Kami:
{$destinationListStr}

ATURAN WAJIB (STRICT GUARDRAILS):
1. R2: JANGAN PERNAH menyertakan harga, angka rupiah (Rp), nominal biaya, diskon, atau estimasi tarif apa pun.
2. CHG-01: Tulis "pemahaman_ringkas" (2 sampai 3 kalimat santun & profesional dalam bahasa klien) yang merangkum masalah, siapa kliennya, dan apa hasil yang diharapkan.
3. CHG-07: Pilih TEPAT 3 destinasi.
   - Utamakan destinasi resmi dari database (beri `sumber: "database"`, sertakan `id_destinasi`).
   - JIKA cerita klien secara spesifik menyebut kota/destinasi yang TIDAK ADA di database (misal ke luar negeri seperti Jepang/Singapura, atau wilayah lain di Indonesia), Anda BOLEH memasukkannya dengan `sumber: "bebas"`, `id_destinasi: null`.
   - Untuk destinasi bebas: DILARANG membuat klaim fasilitas resmi atau jam buka.
4. Rancang susunan hari (1 hingga 4 hari kegiatan) dengan nama kegiatan yang relevan.
5. Berikan maksimal 2 pertanyaan lanjutan untuk memperjelas kebutuhan.
6. WAJIB keluarkan respons HANYA dalam format JSON valid tanpa tanda markdown (tanpa ```json ... ```), mengikuti skema berikut:
{
  "pemahaman_ringkas": "...",
  "tipe_klien": "korporat" | "kampus" | "instansi" | "umum" | null,
  "layanan_disarankan": ["Tour & Wisata"],
  "destinasi": [
    {
      "id_destinasi": 123 atau null,
      "nama": "Nama Destinasi",
      "sumber": "database" atau "bebas",
      "alasan": "Alasan singkat 1 kalimat dikaitkan dengan cerita",
      "kecocokan": 85
    }
  ],
  "hari": [
    {
      "hari": 1,
      "judul": "Hari 1: Kedatangan & Aktivitas",
      "kegiatan": ["Kegiatan A", "Kegiatan B"]
    }
  ],
  "pertanyaan_lanjutan": ["Pertanyaan 1", "Pertanyaan 2"],
  "disclaimer": "Gambaran awal ini disusun otomatis oleh AI untuk membantu perencanaan. Rincian final dan anggaran resmi akan disusun dan dikonfirmasi langsung oleh tim konsultan Empirise.",
  "gambaran_awal": true
}
PROMPT;

        $result = $this->callGeminiJson($prompt);

        if (! $result || ! $this->isValidOutput($result)) {
            Log::info('ItineraryGenerator: Fallback triggered');
            $result = $this->generateFallback($cleanStory, $tipeKlien, $durasiHari, $databaseDestinations);
        }

        $latencyMs = (int) round((microtime(true) - $startTime) * 1000);

        // Catat ke rekomendasi_log (R3: jangan catat PII mentah)
        RecommendationLog::create([
            'preferensi_json' => [
                'tipe_klien' => $tipeKlien,
                'durasi_hari' => $durasiHari,
                'panjang_cerita' => mb_strlen($cleanStory),
            ],
            'hasil_json' => $result,
            'latensi_ms' => $latencyMs,
        ]);

        return $result;
    }

    private function callGeminiJson(string $prompt): ?array
    {
        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return null;
        }

        foreach (self::GEMINI_MODELS as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
                $response = Http::timeout(25)->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [['text' => $prompt]],
                        ],
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 2000,
                        'temperature' => 0.3,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

                if ($response->successful()) {
                    $text = (string) $response->json('candidates.0.content.parts.0.text', '');
                    $text = trim(preg_replace('/^```(?:json)?|```$/m', '', $text) ?? '');
                    $data = json_decode($text, true);

                    if (is_array($data) && $this->isValidOutput($data)) {
                        return $data;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("ItineraryGenerator Gemini {$model} error: {$e->getMessage()}");
            }
        }

        return null;
    }

    private function isValidOutput(array $data): bool
    {
        if (empty($data['pemahaman_ringkas']) || empty($data['destinasi']) || count($data['destinasi']) !== 3) {
            return false;
        }

        // R2 Guardrail: Pastikan tidak memuat pola harga
        $encoded = json_encode($data);
        if (preg_match('/Rp\s*[\d.,]+/i', $encoded) || preg_match('/\b\d+\s*(ribu|juta|rb|jt)\b/i', $encoded)) {
            return false;
        }

        return true;
    }

    private function generateFallback(string $story, ?string $tipeKlien, ?int $durasiHari, $databaseDestinations): array
    {
        $duration = max(1, min(4, $durasiHari ?: 2));
        $tipe = $tipeKlien ?: (preg_match('/kampus|mahasiswa|studi/i', $story) ? 'kampus' : (preg_match('/kantor|perusahaan|karyawan|pt/i', $story) ? 'korporat' : 'umum'));

        $summary = 'Kami memahami Anda sedang merencanakan kegiatan perjalanan untuk kelompok '.($tipe !== 'umum' ? $tipe : 'rombongan').' dengan fokus pada kelancaran koordinasi, kenyamanan peserta, dan pencapaian tujuan kebersamaan.';

        // Pilih 3 destinasi dari database jika ada, atau default
        $destinations = [];
        $candidates = $databaseDestinations->take(3);
        if ($candidates->count() === 3) {
            foreach ($candidates as $cand) {
                $destinations[] = [
                    'id_destinasi' => $cand->id_konten,
                    'nama' => $cand->judul,
                    'sumber' => 'database',
                    'alasan' => 'Destinasi strategis yang cocok untuk kegiatan rombongan dan mudah diakses.',
                    'kecocokan' => 88,
                ];
            }
        } else {
            $destinations = [
                ['id_destinasi' => null, 'nama' => 'Yogyakarta', 'sumber' => 'database', 'alasan' => 'Destinasi kaya budaya dan sarana rombongan lengkap.', 'kecocokan' => 92],
                ['id_destinasi' => null, 'nama' => 'Malang & Batu', 'sumber' => 'database', 'alasan' => 'Suasana sejuk pegunungan ideal untuk keakraban tim.', 'kecocokan' => 87],
                ['id_destinasi' => null, 'nama' => 'Solo', 'sumber' => 'database', 'alasan' => 'Kombinasi wisata kuliner, tradisi, dan kemudahan logistik.', 'kecocokan' => 84],
            ];
        }

        $days = [];
        for ($i = 1; $i <= $duration; $i++) {
            $days[] = [
                'hari' => $i,
                'judul' => "Hari {$i}: ".($i === 1 ? 'Keberangkatan & Pembukaan' : ($i === $duration ? 'Kegiatan Puncak & Perjalanan Pulang' : 'Eksplorasi & Aktivitas Utama')),
                'kegiatan' => [
                    'Sesi koordinasi rombongan dan pembagian tim pendamping',
                    'Kunjungan destinasi dan program kegiatan tematik',
                    'Santap bersama dan evaluasi kegiatan harian',
                ],
            ];
        }

        return [
            'pemahaman_ringkas' => $summary,
            'tipe_klien' => $tipe,
            'layanan_disarankan' => ['Tour & Wisata', 'Event & Gathering'],
            'destinasi' => $destinations,
            'hari' => $days,
            'pertanyaan_lanjutan' => [
                'Berapa perkiraan total jumlah peserta yang akan ikut serta?',
                'Apakah ada preferensi titik kumpul keberangkatan khusus?',
            ],
            'disclaimer' => 'Gambaran awal ini disusun otomatis oleh AI untuk membantu perencanaan. Rincian final dan anggaran resmi akan disusun dan dikonfirmasi langsung oleh tim konsultan Empirise.',
            'gambaran_awal' => true,
        ];
    }
}
