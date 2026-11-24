<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdminContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class WebsiteContentSeeder extends Seeder
{
    /**
     * Seed the content currently displayed on the public homepage.
     */
    public function run(): void
    {
        $this->seedContent('destinasi', [
            ['Yogyakarta', 'yogyakarta', 'Kota budaya dengan candi, keraton, kuliner, dan pusat kerajinan yang ramah untuk rombongan besar.', ['wilayah' => 'Jawa Tengah & DIY', 'kategori' => 'Budaya', 'image' => 'images/figma/yogyakarta.png']],
            ['Solo', 'solo', 'Kota batik dan keraton dengan kuliner khas, pasar tradisional, dan suasana yang tenang.', ['wilayah' => 'Jawa Tengah & DIY', 'kategori' => 'Budaya', 'image' => 'images/figma/solo.png']],
            ['Semarang', 'semarang', 'Jelajahi kota tua, kekayaan kuliner, dan bangunan bersejarah di Semarang.', ['wilayah' => 'Jawa Tengah & DIY', 'kategori' => 'Budaya', 'image' => 'images/figma/semarang.png']],
            ['Dieng', 'dieng', 'Nikmati udara pegunungan, telaga, dan lanskap alam dataran tinggi Dieng.', ['wilayah' => 'Jawa Tengah & DIY', 'kategori' => 'Alam', 'image' => 'images/figma/dieng.png']],
            ['Malang', 'malang', 'Temukan suasana sejuk, wisata alam, dan ragam kuliner khas Malang.', ['wilayah' => 'Jawa Timur', 'kategori' => 'Alam', 'image' => 'images/figma/malang.png']],
            ['Bali', 'bali', 'Rancang perjalanan untuk menikmati alam, budaya, dan pesisir Bali.', ['wilayah' => 'Bali', 'kategori' => 'Alam', 'image' => 'images/figma/bali.png']],
            ['Lombok', 'lombok', 'Jelajahi pantai, pulau-pulau kecil, dan pemandangan Lombok.', ['wilayah' => 'Nusa Tenggara', 'kategori' => 'Alam', 'image' => 'images/figma/lombok.png']],
            ['Labuan Bajo', 'labuan-bajo', 'Mulai perjalanan laut dan jelajahi pesona kepulauan Labuan Bajo.', ['wilayah' => 'Nusa Tenggara', 'kategori' => 'Alam', 'image' => 'images/figma/labuan-bajo.png']],
            ['Bandung', 'bandung', 'Nikmati udara sejuk, suasana kota, dan destinasi sekitar Bandung.', ['wilayah' => 'Jawa Barat', 'kategori' => 'Alam', 'image' => 'images/figma/bandung.png']],
            ['Jakarta', 'jakarta', 'Jelajahi ragam pengalaman kota dan destinasi bersejarah di Jakarta.', ['wilayah' => 'DKI Jakarta', 'kategori' => 'Kota', 'image' => 'images/figma/jakarta.png']],
            ['Banyuwangi', 'banyuwangi', 'Temukan wisata alam dan budaya di ujung timur Pulau Jawa.', ['wilayah' => 'Jawa Timur', 'kategori' => 'Alam', 'image' => 'images/figma/banyuwangi.png']],
            ['Karimunjawa', 'karimunjawa', 'Rencanakan perjalanan ke gugusan pulau dan perairan Karimunjawa.', ['wilayah' => 'Jawa Tengah & DIY', 'kategori' => 'Alam', 'image' => 'images/figma/karimunjawa.png']],
        ]);

        $this->seedContent('layanan', [
            ['Tour & Wisata', 'tour-wisata', 'Perjalanan wisata domestik dan internasional dengan itinerary yang disesuaikan.', ['icon' => 'map']],
            ['Event & Gathering', 'event-gathering', 'Perencanaan acara perusahaan, gathering, dan perayaan dari konsep hingga pelaksanaan.', ['icon' => 'calendar']],
            ['Outbound', 'outbound', 'Program outbound dan fun games dengan fasilitator berpengalaman.', ['icon' => 'gamepad']],
            ['Rental Armada', 'rental-armada', 'Bus dan minibus untuk perjalanan kelompok bersama pengemudi profesional.', ['icon' => 'bus']],
            ['Team Building', 'team-building', 'Kegiatan kolaboratif untuk mengembangkan kerja sama dan kebersamaan tim.', ['icon' => 'users']],
            ['Kunjungan Industri & Edukasi', 'kunjungan-industri-edukasi', 'Program kunjungan perusahaan, pabrik, dan institusi pendidikan dengan fasilitasi audiensi profesional.', ['icon' => 'building']],
            ['Pelatihan & Pengembangan SDM', 'pelatihan-pengembangan-sdm', 'Workshop, pelatihan kepemimpinan, dan pengembangan kapasitas kerja sama tim dengan kurikulum terarah.', ['icon' => 'award']],
            ['Hiburan & Awarding', 'hiburan-awarding', 'Penyelenggaraan malam apresiasi, awarding night, konser intim, dan hiburan panggung berkelas.', ['icon' => 'sparkles']],
        ]);

        $this->seedContent('portofolio', [
            ['Rapat Kerja Nasional Instansi', 'rapat-kerja-nasional-instansi', null, ['lokasi' => 'Bandung', 'kategori' => 'Instansi', 'tahun' => '2025', 'image' => 'images/figma/bandung.png']],
            ['Gathering Tahunan 400 Karyawan', 'gathering-tahunan-400-karyawan', null, ['lokasi' => 'Yogyakarta', 'kategori' => 'Korporat', 'tahun' => '2024', 'image' => 'images/figma/yogyakarta.png']],
            ['Studi Lapangan Mahasiswa Arsitektur', 'studi-lapangan-mahasiswa-arsitektur', null, ['lokasi' => 'Bali', 'kategori' => 'Kampus', 'tahun' => '2024', 'image' => 'images/figma/bali.png']],
            ['Outbound Pegawai Dinas', 'outbound-pegawai-dinas', null, ['lokasi' => 'Bogor', 'kategori' => 'Instansi', 'tahun' => '2023', 'image' => 'images/figma/malang.png']],
            ['Team Building Startup Teknologi', 'team-building-startup-teknologi', null, ['lokasi' => 'Labuan Bajo', 'kategori' => 'Korporat', 'tahun' => '2025', 'image' => 'images/figma/labuan-bajo.png']],
            ['Wisuda & Wisata Angkatan', 'wisuda-wisata-angkatan', null, ['lokasi' => 'Malang', 'kategori' => 'Kampus', 'tahun' => '2025', 'image' => 'images/figma/malang.png']],
        ]);

        $this->seedContent('klien', [
            ['SMP Insan Rabbani', 'smp-insan-rabbani', null, ['bagian' => 'beranda']],
            ['UTY', 'uty', null, ['bagian' => 'beranda']],
            ['UAD', 'uad', null, ['bagian' => 'beranda']],
            ['UNY', 'uny', null, ['bagian' => 'beranda']],
            ['KPU', 'kpu', null, ['bagian' => 'beranda']],
            ["Mu'allimin", 'muallimin', null, ['bagian' => 'beranda']],
            ['BAZNAS', 'baznas', null, ['bagian' => 'beranda']],
            ['The Rain', 'the-rain', null, ['bagian' => 'beranda']],
            ['PN Wates', 'pn-wates', null, ['bagian' => 'beranda']],
            ['Ngatmombilung', 'ngatmombilung', null, ['bagian' => 'beranda']],
            ['Pendheza', 'pendheza', null, ['bagian' => 'beranda']],
            ['Lembaga Yogya', 'lembaga-yogya', null, ['bagian' => 'beranda']],
            ['Kemendikdasmen', 'kemendikdasmen', null, ['bagian' => 'beranda']],
            ['KAI', 'kai', null, ['bagian' => 'beranda']],
            ['Pertamina', 'pertamina', null, ['bagian' => 'beranda']],
            ['ADHI', 'adhi', null, ['bagian' => 'beranda']],
            ['IALF', 'ialf', null, ['bagian' => 'beranda']],
            ['Samsung', 'samsung', null, ['bagian' => 'beranda']],
            ['KONE', 'kone', null, ['bagian' => 'beranda']],
            ['Rocket Chicken', 'rocket-chicken', null, ['bagian' => 'beranda']],
            ['Muhammadiyah Wirobrajan', 'muhammadiyah-wirobrajan', null, ['bagian' => 'beranda']],
            ['Poltek YKPN', 'poltek-ykpn', null, ['bagian' => 'beranda']],
            ['Kemenag Lembaga', 'kemenag-lembaga', null, ['bagian' => 'beranda']],
            ['Kementerian', 'kementerian-tentang', null, ['bagian' => 'tentang']],
            ['HUT RI', 'hut-ri', null, ['bagian' => 'tentang']],
            ['KONE', 'kone-tentang', null, ['bagian' => 'tentang']],
            ['MunclinCorp', 'munclincorp', null, ['bagian' => 'tentang']],
            ['IAF', 'iaf-tentang', null, ['bagian' => 'tentang']],
            ['AMCOA', 'amcoa', null, ['bagian' => 'tentang']],
            ['BBM UAD', 'bbm-uad', null, ['bagian' => 'tentang']],
            ['UTY', 'uty-tentang', null, ['bagian' => 'tentang']],
            ['Mualimin', 'mualimin-tentang', null, ['bagian' => 'tentang']],
        ]);

        $this->seedContent('testimoni', [
            ['HR Manager, perusahaan logistik', 'testimoni-perusahaan-logistik', 'Eksekusi rapi, tim sangat responsif sampai hari terakhir.', ['bagian' => 'beranda', 'penulis' => 'HR Manager, perusahaan logistik']],
            ['Dosen Pembimbing', 'testimoni-dosen-pembimbing', 'Itinerary edukatif dan sesuai anggaran kampus.', ['bagian' => 'beranda', 'penulis' => 'Dosen Pembimbing']],
            ['Kepala Bagian Umum', 'testimoni-kepala-bagian-umum', 'Peserta antusias dan kegiatan berjalan aman.', ['bagian' => 'beranda', 'penulis' => 'Kepala Bagian Umum']],
            ['Ust. Agus Mianta, S.Si., M.Pd.', 'testimoni-agus-mianta', 'Tim kami sangat terbantu dengan Empirise atas perencanaan perjalanan, respons tim, dan koordinasi kegiatan.', ['bagian' => 'tentang', 'penulis' => 'Ust. Agus Mianta, S.Si., M.Pd.', 'jabatan' => 'Direktur, Madrasah Muallimin Yogyakarta']],
            ['Bpk Donny', 'testimoni-donny', 'Tim kami sangat terbantu dengan Empirise atas perencanaan perjalanan, respons tim, dan koordinasi kegiatan.', ['bagian' => 'tentang', 'penulis' => 'Bpk Donny', 'jabatan' => 'Ketua Pandu Employee Gathering KONE Glitchai']],
            ['Bpk Budi Silaban', 'testimoni-budi-silaban', 'Tim kami sangat terbantu dengan Empirise atas perencanaan perjalanan, respons tim, dan koordinasi kegiatan.', ['bagian' => 'tentang', 'penulis' => 'Bpk Budi Silaban', 'jabatan' => 'Managing Director PT. KONE Indonesia']],
        ]);

        $this->seedContent('faq', [
            ['Layanan apa saja yang tersedia?', 'layanan-apa-saja-yang-tersedia', 'Kami melayani tour & wisata, event & gathering, outbound, rental armada, dan team building.', []],
            ['Bisakah itinerary disesuaikan dengan kebutuhan kami?', 'bisakah-itinerary-disesuaikan', 'Ya. Destinasi, kegiatan, durasi, dan kebutuhan rombongan dapat dibicarakan saat konsultasi.', []],
            ['Bagaimana cara mengetahui biaya kegiatan?', 'cara-mengetahui-biaya-kegiatan', 'Biaya disusun berdasarkan kebutuhan dan rincian kegiatan. Hubungi tim kami untuk membahas perkiraan yang sesuai.', []],
            ['Kapan sebaiknya kami mulai merencanakan?', 'kapan-sebaiknya-mulai-merencanakan', 'Hubungi kami lebih awal agar tersedia waktu untuk membahas pilihan tanggal, perjalanan, dan persiapan kegiatan.', []],
        ]);

        $this->seedContent('tim', [
            ['Management Team', 'management-team', 'Juru pikir dan juru lambak.', []],
            ['Operations Team', 'operations-team', 'Penganut Faham “Oke, siap!”.', []],
            ['Support Team', 'support-team', 'Penganut Faham “Oke, bisa!”.', []],
        ]);
    }

    /**
     * @param  list<array{0: string, 1: string, 2: ?string, 3: array<string, string>}>  $records
     */
    private function seedContent(string $type, array $records): void
    {
        foreach ($records as $index => [$title, $slug, $description, $metadata]) {
            $contentExists = AdminContent::query()
                ->where('jenis', $type)
                ->where(function (Builder $query) use ($slug, $title): void {
                    $query->where('slug', $slug)
                        ->orWhere(function (Builder $query) use ($title): void {
                            $query->whereNull('slug')
                                ->where('judul', $title);
                        });
                })
                ->exists();

            if ($contentExists) {
                continue;
            }

            AdminContent::query()->create([
                'jenis' => $type,
                'judul' => $title,
                'slug' => $slug,
                'deskripsi' => $description,
                'metadata' => ['contoh' => true, ...$metadata],
                'status' => 'tampil',
                'aktif' => true,
                'urutan' => $index + 1,
            ]);
        }
    }
}
