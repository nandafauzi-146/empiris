<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ChatbotFaq;
use Illuminate\Database\Seeder;

class ChatbotFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'kata_kunci' => ['layanan', 'paket', 'produk', 'apa saja', 'tour', 'gathering', 'outbound', 'rental', 'armada', 'team building'],
                'pertanyaan' => 'Apa saja layanan yang tersedia di Empirise?',
                'jawaban' => 'Empirise Jalan-Jalan melayani 5 program utama: (1) Tour & Wisata kustom, (2) Event & Gathering perusahaan atau instansi, (3) Outbound & fun games, (4) Rental Armada bus dan minibus berizin resmi bersama driver berpengalaman, serta (5) Team Building. Setiap program dapat dikustomisasi penuh sesuai kebutuhan rombongan Anda.',
                'aktif' => true,
            ],
            [
                'kata_kunci' => ['biaya', 'harga', 'tarif', 'ongkos', 'estimasi', 'berapa', 'paket berapa'],
                'pertanyaan' => 'Berapa biaya atau harga paket kegiatan di Empirise?',
                'jawaban' => 'Harga disusun secara kustom oleh tim kami dalam bentuk surat penawaran resmi, disesuaikan dengan destinasi, jumlah peserta, fasilitas, dan durasi acara Anda. Kami tidak menampilkan harga kaku di awal agar Anda mendapat biaya paling efisien. Silakan mulai konsultasi gratis untuk mendapatkan draf penawaran resmi.',
                'aktif' => true,
            ],
            [
                'kata_kunci' => ['proses', 'cara', 'alur', 'konsultasi', 'pesan', 'booking', 'langkah', 'order', 'mulai'],
                'pertanyaan' => 'Bagaimana alur dan cara memesan kegiatan di Empirise?',
                'jawaban' => 'Alur kerja kami sangat praktis: (1) Isi form kebutuhan di halaman Konsultasi, (2) Tim admin meninjau kebutuhan dan menyusun penawaran resmi, (3) Anda menyetujui penawaran yang cocok, lalu (4) Pembayaran termin dan persiapan kegiatan langsung kami laksanakan.',
                'aktif' => true,
            ],
            [
                'kata_kunci' => ['jadwal', 'tanggal', 'ketersediaan', 'kapan', 'buka', 'waktu', 'slot', 'tersedia'],
                'pertanyaan' => 'Apakah tanggal dan jadwal kegiatan masih tersedia?',
                'jawaban' => 'Ketersediaan tanggal, tim fasilitator, dan armada kendaraan dikonfirmasi langsung oleh admin setelah Anda mengirimkan konsultasi. Kami sarankan Anda mengajukan rencana lebih awal agar ketersediaan tanggal impian Anda dapat dipastikan.',
                'aktif' => true,
            ],
            [
                'kata_kunci' => ['bayar', 'pembayaran', 'dp', 'transfer', 'rekening', 'lunas', 'termin', 'cicilan', 'invoice'],
                'pertanyaan' => 'Bagaimana sistem dan cara pembayaran di Empirise?',
                'jawaban' => 'Pembayaran dilakukan secara transparan melalui transfer ke rekening bank resmi Empirise setelah penawaran disetujui (deal). Pembayaran dibagi ke dalam beberapa termin (uang muka/DP dan pelunasan menjelang kegiatan). Klien dapat mengunggah bukti pembayaran di dashboard akun klien.',
                'aktif' => true,
            ],
            [
                'kata_kunci' => ['armada', 'kendaraan', 'bus', 'minibus', 'hiace', 'elf', 'mobil', 'sewa bus'],
                'pertanyaan' => 'Kendaraan apa saja yang tersedia untuk rental armada?',
                'jawaban' => 'Armada kami meliputi Big Bus (45-59 seat), Medium Bus (30-35 seat), Minibus (Toyota HiAce & Isuzu Elf), serta mobil MPV/SUV keluarga. Seluruh unit terawat dengan fasilitas AC dingin, audio, reclining seat, dan didampingi pengemudi profesional berlisensi.',
                'aktif' => true,
            ],
            [
                'kata_kunci' => ['itinerary', 'kustom', 'ubah', 'rute', 'destinasi', 'rundown', 'ganti jadwal'],
                'pertanyaan' => 'Apakah itinerary dan jadwal kegiatan bisa disesuaikan?',
                'jawaban' => 'Tentu bisa! Prinsip kami adalah menyesuaikan kegiatan dengan kebutuhan klien. Anda dapat mengajukan request destinasi, aktivitas khusus, tempat makan favorit, maupun durasi kegiatan saat pengisian formulir konsultasi.',
                'aktif' => true,
            ],
            [
                'kata_kunci' => ['jam operasional', 'jam kerja', 'jam buka', 'kantor', 'hari kerja', 'kapan dibalas'],
                'pertanyaan' => 'Kapan jam operasional layanan Empirise?',
                'jawaban' => 'Layanan operasional kami buka hari Senin sampai Sabtu pukul 08.00 hingga 17.00 WIB. Konsultasi dan pertanyaan yang masuk di luar jam layanan akan kami respon secepatnya di hari kerja berikutnya.',
                'aktif' => true,
            ],
            [
                'kata_kunci' => ['destinasi', 'tujuan', 'lokasi', 'tempat', 'jogja', 'yogyakarta', 'solo', 'dieng', 'bali', 'lombok', 'bandung', 'malang', 'bromo'],
                'pertanyaan' => 'Destinasi wisata mana saja yang dilayani oleh Empirise?',
                'jawaban' => 'Kami melayani perjalanan ke berbagai destinasi unggulan Indonesia seperti Yogyakarta, Solo, Semarang, Dataran Tinggi Dieng, Malang & Bromo, Banyuwangi, Bandung, Bali, Lombok, hingga Labuan Bajo.',
                'aktif' => true,
            ],
            [
                'kata_kunci' => ['halo', 'hai', 'pagi', 'siang', 'sore', 'malam', 'assalamualaikum', 'bantu'],
                'pertanyaan' => 'Halo, apa yang bisa dibantu?',
                'jawaban' => 'Halo! Saya Asisten AI Empirise Jalan-Jalan. Saya siap membantu menjawab informasi tentang layanan tour, gathering, outbound, rental armada, atau alur konsultasi kami. Ada yang ingin Anda tanyakan?',
                'aktif' => true,
            ],
        ];

        foreach ($faqs as $data) {
            ChatbotFaq::updateOrCreate(
                ['pertanyaan' => $data['pertanyaan']],
                [
                    'kata_kunci' => $data['kata_kunci'],
                    'jawaban' => $data['jawaban'],
                    'aktif' => $data['aktif'],
                ]
            );
        }
    }
}
