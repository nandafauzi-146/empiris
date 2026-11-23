# PRD: Website Empirise Jalan-Jalan

| | |
|---|---|
| **Produk** | Website pemesanan kegiatan kustom Empirise Jalan-Jalan |
| **Versi** | 1.0 (Draft) |
| **Tanggal** | 6 November 2026 |
| **Stack** | Laravel 13 (PHP 8.3 sampai 8.5) + MySQL 8 / MariaDB |
| **Dokumen terkait** | `analsis_pweb_empiris.docx` (analisis sistem), Panduan Membangun Website Empirise (desain ke kode), desain Figma UI-UX-EMPIRISE |

---

## 1. Ringkasan

Empirise Jalan-Jalan adalah biro wisata yang melayani tour, event dan gathering, outbound, rental armada, dan team building untuk perusahaan, kampus, dan instansi. Kegiatan mereka disusun sesuai kebutuhan klien, bukan paket tetap.

Produk ini adalah website tempat seluruh proses dari konsultasi sampai deal berlangsung online. Ada tiga hal yang membedakannya dari company profile biasa:

1. **Deal lewat website.** Konsultasi, penawaran, persetujuan, proyek, dan pembayaran tercatat di satu sistem.
2. **AI yang berangkat dari kebutuhan pelanggan.** Calon klien bercerita bebas, AI memahami kebutuhannya, lalu merekomendasikan layanan dan destinasi serta membuat itinerary gambaran kasar.
3. **Email otomatis dari admin.** Klien selalu mendapat kabar di setiap tahap, tanpa admin mengetik ulang.

Prinsip produk: **mulai dari kebutuhan pelanggan, lalu biro yang menyesuaikan.**

## 2. Latar Belakang dan Masalah

| Masalah saat ini | Dampak |
|---|---|
| Permintaan klien masuk lewat WhatsApp dan telepon, tersebar dan tidak terdokumentasi | Permintaan terlewat, riwayat sulit dilacak |
| Calon klien sulit membayangkan bentuk kegiatan sebelum menghubungi biro | Banyak yang berhenti sebelum berkonsultasi |
| Penawaran disusun manual dan dikirim lewat chat | Versi revisi tidak rapi, status deal tidak jelas |
| Admin menjawab pertanyaan yang sama berulang kali | Waktu admin terbuang, respons lambat |
| Tidak ada pengingat otomatis ke klien | Calon klien menghilang setelah beberapa hari |

## 3. Tujuan dan Metrik Keberhasilan

### 3.1 Tujuan

- **G1.** Memindahkan proses konsultasi sampai deal ke website.
- **G2.** Membantu calon klien membayangkan kegiatannya lewat itinerary AI, sehingga lebih banyak yang berkonsultasi.
- **G3.** Mempercepat kerja admin lewat ringkasan kebutuhan otomatis, draf penawaran dari itinerary, dan email otomatis.
- **G4.** Menampilkan citra biro yang kredibel lewat portofolio, klien, dan testimoni.

### 3.2 Metrik (target awal, perlu divalidasi dengan biro)

| Metrik | Cara ukur | Target awal |
|---|---|---|
| Konversi pengunjung ke konsultasi | Konsultasi baru / pengunjung unik | Ditetapkan setelah 1 bulan data dasar |
| Porsi konsultasi yang berasal dari fitur Rencana AI | Konsultasi dengan `id_itinerary` / total konsultasi | 30% setelah 3 bulan |
| Waktu balasan pertama admin | Selisih konsultasi dibuat dan balasan pertama | Kurang dari 1 hari kerja |
| Konsultasi yang berlanjut ke penawaran | Penawaran dikirim / konsultasi | Naik dibanding kondisi awal |
| Penawaran yang berakhir deal | Deal / penawaran dikirim | Naik dibanding kondisi awal |
| Keberhasilan itinerary AI | Itinerary valid / permintaan itinerary | Di atas 90% |
| Kualitas rekomendasi | Precision@5 dari uji dengan responden | Ditetapkan bersama biro |

### 3.3 Bukan Tujuan (Non-goals)

- Pembayaran online lewat payment gateway. Pembayaran dicatat manual oleh admin beserta bukti bayar.
- Harga otomatis atau kalkulator biaya. Harga hanya muncul di penawaran yang disusun admin.
- Live chat real-time dengan manusia. Percakapan klien dan admin tersedia sebagai chat asinkron di halaman konsultasi (polling), ditambah email.
- Aplikasi mobile native. Website responsif sudah cukup.
- Pemesanan tiket pihak ketiga (hotel, transportasi umum).

## 4. Pengguna

| Persona | Siapa | Kebutuhan utama |
|---|---|---|
| **Calon klien (pengunjung)** | HR, panitia kampus, staf instansi, atau perorangan yang baru mencari biro | Memahami layanan, menilai kredibilitas, mendapat gambaran kegiatan, mudah menghubungi |
| **Klien (login)** | Calon klien yang sudah mendaftar atau punya konsultasi | Memantau konsultasi, menerima dan menanggapi penawaran, melihat proyek dan pembayaran |
| **Admin** | Staf Empirise | Mengelola konsultasi, penawaran, proyek, pembayaran, dan konten |
| **Owner** | Pemilik atau manajer biro | Melihat ringkasan dan laporan |

## 5. Ruang Lingkup

### 5.1 Dalam lingkup (MVP)

- Halaman publik: Beranda, Tentang, Layanan, Portofolio, Destinasi, Rekomendasi, Kalender Agenda, FAQ dan Kontak.
- Registrasi dan login klien, form konsultasi.
- Rekomendasi destinasi (TF-IDF dan cosine similarity) dan itinerary AI.
- Chatbot Publik dan Asisten Akun.
- Dashboard klien: konsultasi, penawaran, proyek, pembayaran, testimoni.
- Dashboard admin: konsultasi, penawaran, proyek, pembayaran, konten, laporan.
- Email otomatis, termasuk follow-up terjadwal.

### 5.2 Fase berikutnya

- Payment gateway.
- Unduh itinerary sebagai PDF.
- Notifikasi WhatsApp.
- Multi-bahasa.
- Evaluasi lanjutan rekomendasi: hybrid dengan embedding dan pencarian vektor (didukung Laravel 13 dan AI SDK).

## 6. Alur Utama (Konsultasi sampai Deal)

1. Calon klien membuka beranda lalu menceritakan rencananya lewat Chatbot Publik atau halaman `/rencana`.
2. AI merangkum kebutuhan, bertanya maksimal 3 pertanyaan bila info penting kurang, lalu menampilkan rekomendasi dan itinerary gambaran kasar tanpa harga.
3. Klien menekan **Kirim ke Empirise** (isi nama dan email bila belum login) atau mengisi form konsultasi `/konsultasi/baru`.
4. Sistem membuat konsultasi, menyimpan itinerary, mengirim email konfirmasi ke klien dan notifikasi ke admin.
5. Admin membalas dari dashboard. Balasan muncul di akun klien dan terkirim lewat email.
6. Admin membuat penawaran, boleh dari itinerary, mengisi harga komponen, lalu mengirimnya.
7. Klien membuka penawaran lalu memilih **Setuju**, **Minta Revisi** (catatan wajib), atau **Tolak**. Revisi membuat versi baru, versi lama tetap tersimpan.
8. Saat **Setuju**, konsultasi menjadi deal, proyek terbentuk otomatis, tanggal masuk kalender, email deal terkirim.
9. Admin membuat termin pembayaran dan mencatat bukti bayar.
10. Setelah kegiatan selesai, klien menerima email permintaan testimoni.

## 7. Kebutuhan Fungsional

Prioritas: **P0** wajib untuk rilis pertama, **P1** penting, **P2** bila waktu ada. Kolom "Analisis" merujuk kode di dokumen analisis bila ada.

### 7.1 Website Publik

| ID | Kebutuhan | Prioritas | Analisis |
|---|---|---|---|
| FR-01 | Beranda berisi navbar, hero dengan slider destinasi, layanan, portofolio, klien, testimoni, CTA, dan footer sesuai desain Figma | P0 | |
| FR-02 | Halaman Tentang, Layanan, Portofolio (daftar dan detail dokumentasi), Destinasi, Kalender Agenda, FAQ dan Kontak | P0 | |
| FR-03 | Halaman Rekomendasi: pengguna memilih preferensi dan melihat destinasi yang cocok | P0 | KF-05 |
| FR-04 | Slider hero mengganti latar, judul, wilayah, dan deskripsi sesuai destinasi aktif | P0 | |
| FR-05 | Responsif dan mobile-first, maksimal 3 klik dari beranda ke form konsultasi | P0 | |

### 7.2 Akun dan Konsultasi

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-10 | Registrasi, login, logout, dan reset password klien lewat email | P0 |
| FR-11 | Form konsultasi: layanan, tujuan, jumlah peserta, durasi, tanggal rencana, minat, cerita kebutuhan | P0 |
| FR-12 | Konsultasi dari pengunjung tanpa akun otomatis ditautkan ke akun setelah registrasi atau login dengan email yang sama | P1 |
| FR-13 | Percakapan konsultasi dua arah antara klien dan admin, dengan status | P0 |
| FR-14 | Klien melihat daftar konsultasi, detail, dan riwayat balasan | P0 |

### 7.3 AI: Rekomendasi dan Itinerary

| ID | Kebutuhan | Prioritas | Analisis |
|---|---|---|---|
| FR-20 | Pengguna menulis cerita bebas (maks 1500 karakter). Sistem mengubahnya menjadi preferensi terstruktur (layanan, tujuan, minat, wilayah, peserta, durasi, budget, kebutuhan khusus) | P0 | KF-32 |
| FR-21 | Bila info penting kurang, AI mengajukan maksimal 3 pertanyaan lanjutan | P0 | KF-32 |
| FR-22 | Peringkat destinasi dihitung dengan TF-IDF dan cosine similarity dari atribut destinasi. AI hanya memakai destinasi dari database | P0 | KF-05, KNF-13 |
| FR-23 | AI menyusun itinerary per hari (jam, aktivitas, destinasi, layanan pendukung, catatan) dari 5 kandidat teratas, tanpa harga dan tanpa janji ketersediaan | P0 | KF-30 |
| FR-24 | Keluaran AI divalidasi. Jika tidak valid, coba ulang sekali, lalu arahkan ke form konsultasi | P0 | |
| FR-25 | Itinerary tampil dengan label gambaran awal dan disclaimer tetap | P0 | |
| FR-26 | Itinerary dapat dikirim ke Empirise sebagai konsultasi baru dan disimpan | P0 | KF-31 |
| FR-27 | Halaman `/rencana` untuk bercerita dan melihat hasil | P1 | |
| FR-28 | Admin menguji preferensi, melihat log rekomendasi, dan mengatur ambang skor serta jumlah hasil | P1 | KF-29 |

### 7.4 Dua Chatbot

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-30 | **Chatbot Publik** untuk pengunjung: menjawab FAQ, merangkum kebutuhan, memberi rekomendasi dan itinerary, mengarahkan ke konsultasi. Tidak menyebut harga dan tidak menjanjikan tanggal | P0 |
| FR-31 | Chatbot Publik tidak dapat mengakses data klien mana pun | P0 |
| FR-32 | Itinerary draft dari chat publik tersimpan lewat sesi dan otomatis ditautkan ke akun setelah login atau registrasi | P1 |
| FR-33 | **Asisten Akun** untuk klien login: merangkum status konsultasi, menjelaskan penawaran, membantu menyusun draf revisi dan pesan ke admin, menampilkan termin dan sisa tagihan, menyusun ulang itinerary | P0 |
| FR-34 | Asisten Akun hanya membaca data milik klien yang sedang login. Identitas klien diambil dari sesi server | P0 |
| FR-35 | Asisten Akun boleh mengutip nominal penawaran dan tagihan milik klien persis dari database, tanpa memperkirakan, menawar, atau menjanjikan diskon | P0 |
| FR-36 | Asisten Akun tidak pernah menyetujui, menolak, atau mengirim apa pun atas nama klien. Semua tindakan lewat tombol Kirim, Ubah, atau Batal yang ditekan klien | P0 |
| FR-37 | Widget otomatis berganti dari Chatbot Publik ke Asisten Akun saat login, di semua halaman | P0 |
| FR-38 | Riwayat percakapan Asisten Akun disimpan 90 hari, lalu dihapus atau dianonimkan | P1 |
| FR-39 | Tombol pintas Tanya Asisten di halaman konsultasi dan penawaran, membuka widget dengan konteks halaman itu | P2 |

### 7.5 Penawaran dan Deal

| ID | Kebutuhan | Prioritas | Analisis |
|---|---|---|---|
| FR-40 | Admin membuat, menyimpan, mengirim, dan membuat versi baru penawaran | P0 | KF-13 |
| FR-41 | Admin membuat draf penawaran dari itinerary: komponen biaya terbentuk dengan harga kosong untuk diisi admin | P0 | KF-34 |
| FR-42 | Klien menanggapi penawaran: Setuju, Minta Revisi (catatan wajib), atau Tolak | P0 | |
| FR-43 | Dialog konfirmasi sebelum Setuju. Halaman sukses menampilkan ringkasan, tanggal, dan langkah berikutnya | P0 | |
| FR-44 | Penawaran yang disetujui membuat proyek otomatis, termasuk jadwal kegiatan, dalam satu transaksi database | P0 | KF-16 |

### 7.6 Proyek, Pembayaran, Testimoni

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-50 | Admin membuat termin pembayaran. Klien mengunggah bukti (jpg, png, pdf, maks 2 MB), admin memverifikasi. Admin juga dapat mencatat langsung | P0 |
| FR-51 | Klien melihat proyek, jadwal, termin, dan status pembayaran | P0 |
| FR-52 | Klien mengisi testimoni setelah proyek selesai. Admin memoderasi sebelum tampil | P1 |

### 7.7 Email Otomatis

| ID | Kebutuhan | Prioritas | Analisis |
|---|---|---|---|
| FR-60 | Sistem mengirim email atas nama `Admin Empirise` dengan Reply-To ke email admin, memuat tombol ke halaman terkait dan tanda tangan biro | P0 | KF-23, KF-33 |
| FR-61 | Email dikirim lewat antrean latar belakang dengan retry 3 kali dan dicatat di `email_log` | P0 | |
| FR-62 | Follow-up terjadwal: pengingat konsultasi 2 hari tanpa tindakan, pengingat penawaran 3 hari tanpa respons, pengingat kegiatan H-7 dan H-1. Maksimal 2 pengingat per konsultasi. Berhenti otomatis bila klien merespons atau status batal atau deal | P1 | |
| FR-63 | Admin dapat mematikan tiap jenis email dan mengedit template dengan variabel | P1 | |

Daftar email:

| Pemicu | Penerima | Subjek |
|---|---|---|
| Konsultasi dikirim | Klien | Konsultasi Anda sudah kami terima |
| Konsultasi dikirim | Admin | Konsultasi baru dari {nama} |
| Klien menekan Kirim ke Empirise | Klien | Gambaran itinerary Anda |
| Admin membalas | Klien | Balasan baru untuk konsultasi Anda |
| 2 hari tanpa tindakan klien | Klien | Masih ingin melanjutkan rencana Anda? |
| Penawaran dikirim | Klien | Penawaran untuk kegiatan Anda (versi {versi}) |
| Penawaran 3 hari tanpa respons | Klien | Penawaran Anda menunggu tanggapan |
| Penawaran disetujui | Klien dan admin | Penawaran disetujui, kegiatan Anda terjadwal |
| Termin dibuat atau diterima | Klien | Informasi pembayaran {termin} |
| H-7 dan H-1 kegiatan | Klien | Pengingat kegiatan Anda |
| Proyek selesai | Klien | Bagaimana kegiatan Anda? |

Semua email bersifat transaksional, bukan promosi.

### 7.8 Admin

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-70 | Dashboard ringkasan: konsultasi baru, penawaran menunggu, proyek berjalan, pembayaran jatuh tempo | P0 |
| FR-71 | Kelola konsultasi dengan panel Pemahaman Kebutuhan dan Itinerary AI | P0 |
| FR-72 | Kelola penawaran, proyek, pembayaran, dan jadwal (kalender) | P0 |
| FR-73 | Kelola konten: layanan, destinasi (dengan tag, deskripsi, foto), portofolio, klien, testimoni, FAQ, FAQ chatbot, tim | P0 |
| FR-74 | Laporan dan ekspor Excel/CSV | P1 |
| FR-75 | Peninjauan log chatbot dan rekomendasi | P1 |

## 8. Aturan dan Pagar Pengaman AI

| Aturan | Penjelasan |
|---|---|
| Tanpa harga di Chatbot Publik dan itinerary | Harga hanya di penawaran admin. Pengecualian: Asisten Akun mengutip nominal milik klien |
| Tanpa janji | AI tidak menjanjikan ketersediaan tanggal, tim, atau armada |
| Hanya data resmi | Destinasi dan layanan hanya dari database. AI tidak boleh mengarang tempat, jam buka, atau fasilitas |
| Privasi | Nama, email, dan nomor HP disamarkan sebelum dikirim ke penyedia AI |
| Kunci aman | API key dan prompt sistem hanya di server (`.env`) |
| Batas laju | 5 itinerary per jam per IP. 30 pesan per sesi. Asisten Akun dibatasi per akun |
| Timeout | 10 detik untuk chat, 30 detik untuk itinerary, dengan tampilan loading |
| Cadangan | Jika AI gagal, arahkan ke form preferensi dan form konsultasi |
| Pencatatan | Panggilan dicatat di `chatbot_log` dan `rekomendasi_log`, tanpa menyimpan cerita sensitif mentah |
| Tindakan terkunci | Asisten hanya menyusun draf. Setuju, tolak, revisi, dan kirim pesan hanya lewat klik klien |
| Disclaimer tetap | Ini gambaran awal untuk membantu diskusi, bukan penawaran. Rincian, harga, dan ketersediaan disusun tim Empirise setelah konsultasi |

## 9. Kebutuhan Non-Fungsional

| Area | Kebutuhan |
|---|---|
| Kinerja | Halaman utama dimuat cepat (target Lighthouse mobile Performance di atas 90). Itinerary selesai dalam 30 detik |
| Keamanan | Password ter-hash (bcrypt), proteksi CSRF, prepared statement lewat Eloquent, validasi di server, Policy kepemilikan data, upload hanya jpg/png/pdf maks 2 MB dengan nama acak |
| Isolasi data | Klien A tidak dapat membaca data klien B, termasuk lewat chatbot. Dibuktikan dengan Feature test dua akun |
| Aksesibilitas | `alt` pada gambar, `aria-label` pada tombol ikon, fokus terlihat, dukungan `prefers-reduced-motion` (target Lighthouse Accessibility di atas 90) |
| Lokalisasi | Bahasa Indonesia. Zona waktu WIB (`Asia/Jakarta`). Uang dalam Rupiah. Tanggal dd MMMM yyyy |
| Keandalan | Antrean email dengan retry. Mode cadangan bila AI gagal |
| Data | MySQL/MariaDB, InnoDB, `utf8mb4` |
| Pemeliharaan | Konfigurasi di `.env`, penyedia SMTP dan model AI dapat diganti tanpa mengubah kode |

## 10. Desain dan UI

Rujukan visual: file Figma UI-UX-EMPIRISE dan screenshot landing page. Nilai di bawah diperkirakan dari screenshot.

- **Font:** Poppins (300 sampai 700).
- **Warna:** primary `#D32F2F`, primary-soft `#FDE8E8`, ink `#1A1A1A`, body `#6B6B6B`, dark `#0A0A0A`, bintang `#FBBF24`.
- **Bentuk:** kartu radius 16 px (slider 24 px), tombol pill, bayangan lembut. Kartu portofolio aktif dengan bayangan merah.
- **Layout:** kontainer ±1112 px, desain referensi 1489 px.
- **Urutan beranda:** navbar, hero dengan slider destinasi, layanan (5 kartu), portofolio (carousel coverflow), klien (23 logo), testimoni (3 kartu), CTA dan footer hitam, tombol chatbot melayang.
- **Responsif:** breakpoint 1280, 1024, 768, di bawah 768.

Spesifikasi rinci tiap section ada di Panduan Membangun Website Empirise (Bagian 4 sampai 6).

## 11. Arsitektur dan Stack

| Lapisan | Pilihan |
|---|---|
| Backend | Laravel 13, PHP 8.3 sampai 8.5 |
| Basis data | MySQL 8 atau MariaDB 10.6+ |
| Tampilan | Blade, Tailwind CSS (Vite), Alpine.js |
| Slider | Swiper (coverflow untuk portofolio) |
| Ikon | Lucide (`blade-lucide-icons`) |
| Autentikasi | Laravel Breeze (Blade), kolom `users.role` (klien, admin), Policy. Breeze kompatibel dengan Laravel 13 tetapi tidak lagi menerima fitur baru. Alternatif: starter kit Livewire resmi atau Fortify dengan view Blade sendiri |
| Admin | Filament 5 di `/admin` (disarankan), memakai Livewire 4 |
| Email | Laravel Mail, Queue driver `database`, Scheduler |
| AI | Laravel AI SDK (`laravel/ai`) dengan provider Anthropic (Claude). Agent untuk ekstraksi preferensi, itinerary, Chatbot Publik, dan Asisten Akun; Tool untuk akses data; structured output untuk skema. Kunci dan model di `.env` dan `config/ai.php` |
| Rekomendasi | Service PHP untuk TF-IDF dan cosine similarity, vektor di `destinasi.vektor_tfidf` |
| Ekspor | `maatwebsite/excel` |
| Pengujian | Pest 4 atau PHPUnit. Panggilan AI diuji dengan fake bawaan AI SDK |

Alur AI: cerita masuk, penyamaran data pribadi, ekstraksi preferensi (LLM), peringkat destinasi (TF-IDF), penyusunan itinerary dari kandidat (LLM), validasi, simpan, tampilkan.

## 12. Model Data

Dasar: 20 tabel di analisis (Bagian 9). Tambahan dari PRD ini:

| Tabel atau kolom | Keterangan |
|---|---|
| `itinerary_draft` | `id_itinerary`, `id_konsultasi` (null), `id_klien` (null), `sesi_id`, `sumber` (publik, akun), `preferensi_json`, `itinerary_json`, `status` (draft, dikirim, dipakai), `model`, `created_at` |
| `email_template` | `jenis`, `subjek`, `isi`, `aktif` |
| `email_log` | `id_email`, `id_konsultasi`, `jenis`, `ke_email`, `subjek`, `status` (terjadwal, terkirim, gagal), `jadwal_kirim`, `terkirim_at`, `error` |
| `konsultasi.id_itinerary` | FK ke `itinerary_draft`, null |
| `konsultasi.preferensi_json` | JSON, null |
| `chatbot_log.mode`, `.id_klien`, `.konteks_json` | Mode publik atau akun, klien (null untuk publik) |

Catatan: model Eloquent memakai nama tabel dan primary key sesuai analisis (misalnya `id_konsultasi`). `email_log` melengkapi kolom `balasan_konsultasi.terkirim_email`.

## 13. Endpoint Utama

| Method | Path | Fungsi |
|---|---|---|
| POST | `/api/chat/publik` | Pesan ke Chatbot Publik (tanpa login, throttle per IP) |
| POST | `/api/chat/akun` | Pesan ke Asisten Akun (wajib login, id klien dari sesi) |
| GET | `/api/chat/akun/riwayat` | Riwayat percakapan akun |
| POST | `/api/chat/akun/draf/{id}/kirim` | Klien mengirim draf pesan atau revisi |
| POST | `/api/ai/preferensi` | Cerita menjadi preferensi atau pertanyaan lanjutan |
| POST | `/api/ai/itinerary` | Buat itinerary, simpan draft |
| GET | `/api/itinerary/{id}` | Ambil itinerary (pemilik sesi atau klien) |
| POST | `/api/itinerary/{id}/kirim` | Buat konsultasi dari itinerary, kirim email |
| POST | `/api/penawaran/{id}/respons` | Klien setuju, revisi, atau tolak |
| (aksi admin) | Buat penawaran dari itinerary | Draf penawaran dengan harga kosong |

Rute halaman utama: `/`, `/tentang`, `/layanan`, `/portofolio`, `/destinasi`, `/rekomendasi`, `/kalender`, `/faq-kontak`, `/rencana`, `/konsultasi/baru`, `/masuk`, `/daftar`, `/akun/...`, `/admin`.

## 14. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| AI mengarang destinasi atau fasilitas | Klien salah paham | AI hanya memakai kandidat dari database, validasi `id_destinasi`, disclaimer |
| Kebocoran data antar klien lewat chatbot | Pelanggaran privasi | Identitas dari sesi, Policy, scope `id_klien`, Feature test dua akun |
| Data pribadi terkirim ke penyedia AI | Pelanggaran privasi | Penyamaran sebelum kirim, catatan privasi di kolom cerita |
| Biaya penggunaan AI membengkak | Biaya tak terkendali | Batas laju, batas panjang pesan, batas biaya di akun API |
| Data destinasi sedikit atau berkualitas rendah | Rekomendasi buruk | Minimal 20 sampai 30 destinasi lengkap dengan tag dan deskripsi sebelum rilis AI |
| Email masuk spam | Klien tidak menerima kabar | SPF, DKIM, DMARC di domain pengirim, template ringkas |
| Layanan AI atau SMTP gagal | Alur terhenti | Mode cadangan ke form, antrean dengan retry, `email_log` |
| Admin terbebani ekspektasi tinggi dari itinerary | Kerja ganda | Itinerary berlabel gambaran awal, penawaran tetap dari admin |
| API Laravel AI SDK dapat berubah antarversi | Fitur AI rusak setelah update | Kunci versi di `composer.json`, bungkus di kelas Agent dan Tool milik sendiri, baca dokumentasi resmi sebelum menulis kode, uji dengan fake |

## 15. Asumsi dan Dependensi

- Biro menyediakan: daftar layanan resmi, data kontak person dan jam layanan, alamat, dokumen legalitas yang boleh ditampilkan, foto, dan logo klien (dengan izin tampil).
- Biro menyediakan atribut destinasi (minimal 20 sampai 30) lengkap dengan tag dan deskripsi.
- Akun email pengirim (domain dan SMTP) disediakan biro.
- Akun API penyedia model AI dan batas biaya ditentukan biro.
- Nominal DP dan skema termin standar ditentukan biro (sementara: DP dan Pelunasan).
- Hosting mendukung PHP 8.3 atau lebih baru (Laravel 13 tidak mendukung PHP 8.2), MySQL, cron (Scheduler), dan proses queue worker.

## 16. Rencana Rilis

| Fase | Isi | Hasil |
|---|---|---|
| 0. Persiapan | Data dari biro, akun SMTP dan AI, setup proyek, design tokens | Proyek siap dibangun |
| 1. Fondasi publik | Landing page dan halaman publik, registrasi dan login, form konsultasi, email dasar, migration dan seeder | Pengunjung bisa berkonsultasi |
| 2. Admin dan deal | Dashboard admin (Filament), balasan, penawaran dan versi, persetujuan klien, proyek otomatis, pembayaran, testimoni | Deal berjalan penuh di website |
| 3. AI | Mesin rekomendasi, ekstraksi preferensi, itinerary, `/rencana`, Chatbot Publik, penautan itinerary ke konsultasi dan penawaran | Calon klien mendapat gambaran awal |
| 4. Asisten dan otomasi | Asisten Akun, follow-up dan pengingat terjadwal, log dan peninjauan | Pendampingan klien otomatis |
| 5. Peluncuran | Uji penerimaan (UAT), uji keamanan dan isolasi data, performa, aksesibilitas, evaluasi rekomendasi | Rilis produksi |

## 17. Kriteria Penerimaan (Ringkas)

- [ ] Pengunjung bisa bercerita, menerima rekomendasi dan itinerary tanpa harga, lalu mengirimnya sebagai konsultasi.
- [ ] Admin menerima notifikasi, membalas, dan membuat penawaran dari itinerary.
- [ ] Klien dapat setuju, minta revisi, atau tolak dari akunnya. Setuju membuat proyek otomatis dan email deal.
- [ ] Semua email di Bagian 7.7 terkirim, tercatat, dan follow-up berhenti saat klien merespons.
- [ ] Itinerary hanya berisi destinasi dari database dan memuat disclaimer.
- [ ] Chatbot Publik tidak dapat mengakses data klien. Asisten Akun hanya membaca data milik klien yang login (diuji dengan dua akun).
- [ ] Asisten Akun tidak melakukan tindakan tanpa klik klien.
- [ ] Tidak ada nama, email, atau nomor HP yang dikirim ke penyedia AI.
- [ ] Saat AI atau SMTP gagal, pengguna diarahkan ke form konsultasi dan email di-retry.
- [ ] Tampilan sesuai desain di lebar 1489, 1280, 1024, 768, dan 390 px, tanpa scroll horizontal.
- [ ] Lighthouse mobile: Performance dan Accessibility di atas 90.

## 18. Pertanyaan Terbuka

1. Daftar layanan resmi dan istilah yang dipakai biro.
2. Nominal DP dan skema termin standar.
3. Data kontak person, alamat, dan dokumen legalitas yang boleh dipajang. Perlu diputuskan juga apakah nomor WhatsApp tampil di footer atau hanya di halaman FAQ dan Kontak.
4. Apakah logo dan nama klien boleh ditampilkan di website.
5. Domain dan SMTP pengirim email.
6. Batas biaya penggunaan AI per bulan.
7. Kriteria evaluasi rekomendasi (misalnya Precision@5) dan calon responden uji.
8. Apakah email itinerary ke klien perlu persetujuan admin (saat ini: tidak, terkirim setelah klien menekan Kirim ke Empirise).
9. Nama dan judul resmi halaman beranda. Judul file desain "Jelajahi Liburan Alam Premium" tidak sesuai dengan konten.
10. Waktu penyimpanan riwayat chat akun (saat ini: 90 hari).

## 19. Lampiran

- `analsis_pweb_empiris.docx`: analisis sistem (aktor, alur, halaman, form, chatbot, ERD, aturan bisnis).
- Panduan Membangun Website Empirise Jalan-Jalan (Laravel + MySQL): spesifikasi desain ke kode dan prompt bertahap untuk AI agent di VSCode.
- Figma: UI-UX-EMPIRISE (node 214-3).