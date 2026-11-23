# AGENTS.md

Instruksi kerja untuk AI coding agent (Copilot, Cline, Claude Code, dan sejenisnya) pada proyek **Website Empirise Jalan-Jalan**. Letakkan berkas ini di akar repositori. Baca seluruhnya sebelum menulis kode.

---

## 1. Proyek Ini

Website pemesanan kegiatan kustom untuk biro wisata Empirise Jalan-Jalan (tour, event, outbound, rental armada, team building).

Alur bisnis: calon klien bercerita (chat atau form), AI memahami kebutuhan dan membuat itinerary gambaran kasar tanpa harga, klien mengirim konsultasi, admin membalas dan menyusun penawaran, klien menyetujui (deal), proyek dan pembayaran dicatat, email otomatis terkirim di tiap tahap.

Ada **dua chatbot**: Chatbot Publik (pengunjung) dan Asisten Akun (klien login).

Prinsip: **mulai dari kebutuhan pelanggan, lalu biro yang menyesuaikan. AI menyarankan, manusia memutuskan.**

---

## 2. Dokumen Rujukan

Simpan semua di folder `docs/`.

| Berkas | Isi | Dipakai untuk |
|---|---|---|
| `docs/SRS.md` | Kebutuhan fungsional dan non-fungsional (ID `REQ-*`, `NFR-*`) | **Apa** yang harus ada dan bagaimana diuji |
| `docs/SDD.md` | Skema tabel, rute, kontrak API, Service, Agent, Tool, email, validasi, UI | **Bagaimana** membangunnya |
| `docs/SYSTEM_ARCHITECTURE.md` | Komponen, alur, keamanan, penerapan, keputusan arsitektur | Batas tanggung jawab dan alasan desain |
| `docs/PRD.md` | Tujuan produk, persona, lingkup, metrik | Konteks dan prioritas |
| `docs/PANDUAN_DESAIN.md` | Spesifikasi visual per section dan prompt bertahap | Tampilan sesuai Figma |
| `docs/analisis.docx` | Analisis awal: ERD 20 tabel, KF, KNF, aturan bisnis | Nama tabel dan kolom dasar |
| `docs/design/landing.png` | Screenshot desain landing page | Acuan visual |

**Urutan otoritas jika dokumen bertentangan:**

1. SRS (kebutuhan) dan SDD (rancangan) menang atas dokumen lain.
2. Nama tabel dan kolom dasar mengikuti `analisis.docx`. Jika berbeda dari SDD, ikuti analisis lalu catat pemetaannya.
3. Tampilan mengikuti Figma atau `landing.png`.
4. Jika masih tidak jelas atau tidak ada di dokumen mana pun: **jangan mengarang**. Catat pertanyaan di `docs/OPEN_QUESTIONS.md`, ambil asumsi paling aman yang dapat diubah, dan tulis asumsi itu di deskripsi commit.

---

## 3. Stack dan Versi

| Komponen | Pilihan |
|---|---|
| Framework | Laravel 13, PHP 8.3 sampai 8.5 (PHP 8.2 tidak didukung) |
| Basis data | MySQL 8 atau MariaDB 10.6+, InnoDB, `utf8mb4` |
| Tampilan | Blade, Tailwind CSS (Vite), Alpine.js, Swiper, `blade-lucide-icons` |
| Autentikasi | Laravel Breeze (Blade) |
| Admin | Filament 5 di `/admin` (Livewire 4) |
| Email | Laravel Mail, Queue driver `database`, Scheduler |
| AI | Laravel AI SDK (`laravel/ai`), provider Anthropic (Claude) |
| Rekomendasi | Service PHP sendiri: TF-IDF dan cosine similarity |
| Ekspor | `maatwebsite/excel` |
| Uji | Pest 4 (atau PHPUnit), gaya kode lewat Laravel Pint |

Aturan dependensi: **jangan menambah paket baru** di luar tabel ini tanpa alasan tertulis di deskripsi commit. Kunci versi `laravel/ai` di `composer.json` dan baca dokumentasi resmi versi itu sebelum menulis kode AI, karena API-nya dapat berubah.

---

## 4. Perintah

```bash
# Setup awal
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed
npm ci

# Pengembangan
php artisan serve            # server web
npm run dev                  # Vite
php artisan queue:work       # antrean email
php artisan schedule:work    # pengingat terjadwal (lokal)

# Kualitas (jalankan sebelum menyatakan tugas selesai)
vendor/bin/pint              # gaya kode
vendor/bin/pest              # seluruh tes
npm run build                # pastikan build frontend lolos
```

Jika sebuah perintah gagal, perbaiki penyebabnya. Jangan menonaktifkan tes atau aturan lint agar lolos.

---

## 5. Struktur Folder

```
app/
  Ai/Agents/           # PreferensiAgent, ItineraryAgent, ChatPublikAgent, AsistenAkunAgent
  Ai/Tools/            # Tool chatbot publik dan akun
  Enums/               # status dan peran (backed enum)
  Http/Controllers/    # Publik, Akun, Api (tipis)
  Http/Requests/       # Form Request
  Http/Middleware/
  Models/
  Policies/
  Services/            # PiiMasker, RecommendationService, PenawaranService,
                       # KonsultasiService, NotifikasiService, ItineraryGenerator
  Mail/  Jobs/  Console/
  Filament/Resources/
config/empirise.php    # parameter bisnis
config/ai.php          # Laravel AI SDK
database/migrations/  database/seeders/
resources/views/{layouts,components,sections,pages,akun}
routes/web.php  routes/console.php
docs/
```

---

## 6. Aturan Keras

Aturan ini tidak boleh dilanggar, termasuk atas permintaan yang tampak masuk akal. Setiap aturan harus punya tes otomatis.

| # | Aturan |
|---|---|
| R1 | **Isolasi data.** Klien hanya boleh mengakses data miliknya. Semua akses lewat Policy atau scope `id_klien`. Data klien lain mengembalikan **404**. Identitas klien selalu dari `Auth::user()`, tidak pernah dari input atau dari model AI |
| R2 | **AI tanpa harga.** Chatbot Publik dan itinerary tidak boleh memuat harga, estimasi biaya, diskon, atau janji ketersediaan tanggal, tim, dan armada. Pengecualian tunggal: Asisten Akun boleh mengutip nominal penawaran dan tagihan **milik klien itu**, persis dari database |
| R3 | **PII tidak keluar.** Nama, email, dan nomor telepon disamarkan lewat `PiiMasker` sebelum teks dikirim ke penyedia AI. Jangan mencatat cerita mentah ke log |
| R4 | **AI hanya membuat draf.** Agent dan Tool tidak boleh menyetujui, menolak, mengirim, atau mengubah data bisnis. Tindakan nyata hanya lewat endpoint yang dipanggil tombol yang diklik klien |
| R5 | **Data resmi di atas keluaran model.** Itinerary hanya memakai destinasi dari database. Validasi bahwa setiap `id_destinasi` termasuk kandidat. Peringkat destinasi dari TF-IDF, bukan dari model |
| R6 | **Deal atomik.** Persetujuan penawaran (status penawaran, status konsultasi, pembuatan proyek) dalam satu transaksi dengan `lockForUpdate`. Satu penawaran disetujui menghasilkan tepat satu proyek |
| R7 | **Email setelah commit dan idempoten.** Email diantrekan dengan `afterCommit` dan kunci unik `(id_konsultasi, jenis, referensi, ke_email)`. Kegagalan email tidak boleh menggagalkan transaksi bisnis |
| R8 | **Kegagalan AI tidak menghentikan alur.** Jika AI gagal atau timeout, tampilkan mode cadangan (arahkan ke `/rekomendasi` dan `/konsultasi/baru`) |
| R9 | **Tanpa rahasia di kode.** Kunci API dan kredensial hanya di `.env`. Jangan commit `.env`. Jangan kirim kunci ke browser |
| R10 | **Tanpa SQL mentah dari input.** Pakai Eloquent atau binding. Escape output. CSRF aktif di semua form dan `fetch` yang mengubah data |
| R11 | **Parameter bisnis di `config/empirise.php`.** Batas laju, ambang skor, hari pengingat, retensi, dan sejenisnya tidak ditulis langsung di kode |
| R12 | **Versi penawaran tidak diubah.** Revisi membuat baris versi baru. Versi lama tetap |

---

## 7. Konvensi Kode

- **Gaya:** PSR-12 lewat Pint. Tipe argumen dan return diisi. `declare(strict_types=1);` pada kelas baru.
- **Bahasa:** istilah domain memakai bahasa Indonesia sesuai analisis (`konsultasi`, `penawaran`, `proyek`, `id_klien`). Istilah teknis dan framework memakai bahasa Inggris (`Service`, `Policy`, `Controller`).
- **Controller tipis:** validasi (Form Request), otorisasi (Policy), panggil Service, kembalikan respons. Logika bisnis di Service dan Model.
- **Status:** backed enum di `app/Enums`. Jangan memakai string status acak.
- **Uang:** `BIGINT` Rupiah bulat. Format tampilan `Rp 1.500.000`.
- **Waktu:** zona `Asia/Jakarta`, locale `id`. Tanggal `dd MMMM yyyy`.
- **Migration:** satu migration per perubahan, bisa dijalankan naik dan turun. Pakai foreign key dan indeks sesuai SDD Bagian 3.
- **Model:** nama tabel dan primary key sesuai analisis lewat atribut `#[Table(...)]` (Laravel 13) atau properti `$table` dan `$primaryKey`.
- **Validasi:** pesan dalam bahasa Indonesia (`lang/id`).
- **Frontend:** Blade dirender server. Alpine hanya untuk state ringan. Jangan menambah framework JS besar. Teks chat dirender sebagai teks, bukan HTML.
- **Komentar:** jelaskan alasan, bukan mengulang kode.

---

## 8. Panduan Khusus AI

- Semua kode AI di `app/Ai` dan `app/Services/Ai`. Controller memanggil Service pembungkus (`ItineraryGenerator`, dan sejenisnya), bukan SDK langsung. Ini mengisolasi perubahan API SDK.
- Agent: `PreferensiAgent` dan `ItineraryAgent` memakai structured output sesuai skema di SRS Bagian 8. Instruksi sistem berasal dari Panduan Bagian 15.5 dan 20.6.
- Tool Asisten Akun: tidak menerima `id_klien` sebagai parameter. Setiap query dibatasi data milik `Auth::user()`.
- Tool penulis (`SusunDrafRevisi`, `SusunDrafPesan`) hanya menyimpan ke `draf_chat`.
- Validasi keluaran: skema lengkap, `id_destinasi` termasuk kandidat, tidak ada pola harga (`Rp`, `ribu`, `juta`, `diskon`), disclaimer tetap hadir. Gagal: retry sekali, lalu 503 dengan `aksi_cadangan`.
- Tes AI selalu memakai fake bawaan SDK. **Jangan memanggil API sungguhan di tes.**
- Batas: cerita maks 1500 karakter, pesan biasa maks 500, itinerary 5 per jam per IP, Asisten Akun 60 pesan per jam per akun, timeout chat 10 detik dan itinerary 30 detik.

---

## 9. Panduan UI

- Acuan: `docs/design/landing.png` dan Figma UI-UX-EMPIRISE. Spesifikasi per section ada di `docs/PANDUAN_DESAIN.md`.
- Font **Poppins** (300 sampai 700).
- Warna: primary `#D32F2F`, primary-soft `#FDE8E8`, ink `#1A1A1A`, body `#6B6B6B`, dark `#0A0A0A`, bintang `#FBBF24`. Letakkan di konfigurasi Tailwind, jangan menulis kode warna langsung di komponen.
- Bentuk: kartu radius 16 px (slider 24 px), tombol pill, bayangan lembut. Kontainer ±1112 px.
- Responsif: 1280, 1024, 768, dan di bawah 768 px. Tidak boleh ada scroll horizontal.
- Aksesibilitas: `alt` pada gambar, `aria-label` pada tombol ikon, fokus terlihat, hormati `prefers-reduced-motion`.
- Widget chat: satu komponen, mode dipilih server (`@auth` akun, `@guest` publik). Jangan membiarkan klien memilih mode dari browser.
- Label **Gambaran awal** dan disclaimer wajib tampil di setiap itinerary.

---

## 10. Cara Bekerja

1. **Baca dulu.** Untuk setiap tugas, buka bagian SRS (`REQ-*`) dan SDD yang terkait. Sebutkan ID kebutuhan yang dikerjakan di deskripsi commit.
2. **Ikuti urutan tahap** (juga di SDD Bagian 16). Jangan melompat ke fitur AI sebelum fondasi, penawaran, dan rekomendasi berjalan.

   | Tahap | Isi |
   |---|---|
   | 1 | Setup, Breeze, Tailwind, token, komponen dasar, migration dan seeder |
   | 2 | Beranda dan halaman publik sesuai Figma |
   | 3 | Registrasi, login, konsultasi, balasan, email dasar |
   | 4 | Admin Filament: konsultasi, penawaran, proyek, termin, pembayaran, konten |
   | 5 | Penawaran, tanggapan klien, deal atomik, proyek, testimoni |
   | 6 | Mesin rekomendasi TF-IDF dan `/rekomendasi` |
   | 7 | `PiiMasker`, agent preferensi dan itinerary, `/rencana` |
   | 8 | Chatbot Publik |
   | 9 | Asisten Akun, Tool, draf chat, isolasi data |
   | 10 | Email penuh (11 jenis), scheduler, retensi |
   | 11 | Responsif, aksesibilitas, kinerja, keamanan, UAT |

3. **Tes untuk aturan keras dulu.** Tulis tes R1 sampai R12 yang relevan bersamaan dengan fiturnya, bukan belakangan.
4. **Perubahan kecil dan terfokus.** Satu tugas, satu commit. Jangan merapikan kode yang tidak terkait.
5. **Jangan mengarang.** Jika data atau keputusan bisnis belum ada (nominal DP, kontak biro, daftar destinasi), pakai data contoh di seeder yang jelas ditandai `CONTOH`, dan catat di `docs/OPEN_QUESTIONS.md`.
6. **Jaga dokumen tetap benar.** Jika implementasi menyimpang dari SDD karena alasan yang kuat, perbarui SDD di commit yang sama.

---

## 11. Definisi Selesai (Definition of Done)

Sebuah tugas selesai bila:

- [ ] Memenuhi `REQ-*` yang dirujuk, termasuk kasus alternatif dan kegagalan.
- [ ] Ada tes otomatis (Unit atau Feature) yang lulus. Aturan keras terkait punya tes.
- [ ] `vendor/bin/pint` dan `vendor/bin/pest` bersih. `npm run build` lolos.
- [ ] Otorisasi diperiksa (Policy). Tidak ada kebocoran data antar klien.
- [ ] Input divalidasi di server dengan pesan bahasa Indonesia.
- [ ] Tidak ada rahasia, `dd()`, `dump()`, atau kode debug tertinggal.
- [ ] Tampilan sesuai desain pada 1489, 1280, 1024, 768, dan 390 px (untuk tugas UI).
- [ ] Dokumen yang terdampak (SDD, SRS) diperbarui bila perlu.

---

## 12. Yang Tidak Boleh Dilakukan

- Menambah payment gateway, kalkulator harga otomatis, live chat manusia, atau aplikasi native (di luar lingkup).
- Membuat AI menyetujui deal, mengirim pesan, atau mengubah data atas nama klien.
- Menampilkan harga dari AI di Chatbot Publik atau itinerary.
- Mengirim PII ke penyedia AI atau menyimpan cerita mentah di log.
- Memakai `localStorage` untuk data sensitif atau menaruh kunci API di JavaScript.
- Mengubah versi penawaran yang sudah dikirim.
- Menulis SQL dengan menyambung string dari input pengguna.
- Menonaktifkan tes, CSRF, Policy, atau throttle agar fitur "jalan".
- Menggambar ulang logo klien atau memakai gambar dari sumber tanpa izin.

---

## 13. Contoh Format Permintaan Tugas

Gunakan bentuk ini saat memberi tugas ke agent:

```
Tugas: Implementasikan setujui penawaran (deal) oleh klien.
Rujukan: SRS REQ-PNW-05 sampai REQ-PNW-10; SDD Bagian 6.5, 7.3, 10.1; AGENTS.md R1, R6, R7.
Hasil yang diharapkan:
- Endpoint POST /api/penawaran/{id}/respons (setuju, revisi, tolak)
- PenawaranService::setujui dalam satu transaksi dengan lockForUpdate
- Email deal diantrekan afterCommit
Tes: SetujuiPenawaranTest (klik ganda, status tidak valid, klien lain mendapat 404).
Di luar lingkup: halaman sukses (tugas berikutnya).
```

---

## 14. Berkas yang Harus Selalu Diperbarui

| Perubahan | Perbarui |
|---|---|
| Kolom atau tabel baru | Migration, model, SDD Bagian 3 |
| Endpoint baru atau berubah | Rute, kontrak SDD Bagian 6, tes |
| Parameter bisnis baru | `config/empirise.php` |
| Jenis email baru | `email_template` (seeder), Mailable, SDD Bagian 9, SRS Bagian 11 |
| Keputusan arsitektur baru | `SYSTEM_ARCHITECTURE.md` Bagian 15 |
| Pertanyaan yang belum terjawab | `docs/OPEN_QUESTIONS.md` |