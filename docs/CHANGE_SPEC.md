# CHANGE SPEC: Penyempurnaan Website Empirise Jalan-Jalan
**Untuk AI coding agent (Antigravity). Simpan sebagai `docs/CHANGE_SPEC.md`.**

| | |
|---|---|
| **Jenis dokumen** | Change Specification (tambahan, bukan pengganti SRS atau SDD) |
| **Versi** | 1.2 |
| **Tanggal** | 24 November 2026 |
| **Stack** | Laravel 13 (PHP 8.3 sampai 8.5), MySQL 8 / MariaDB, Blade + Alpine.js + Tailwind CSS (Vite), Breeze. **Tanpa Filament dan tanpa Livewire** |
| **Basis** | Proyek yang sudah berjalan. SRS v1.1, SDD, AGENTS.md, PRD, ADMIN_UI_SPEC |
| **Sumber keputusan** | Wawancara owner Empirise, prototipe interaktif (HTML) hasil iterasi, keputusan pemilik proyek |

---

## 0. Cara Memakai Dokumen Ini (BACA DULU)

Proyek ini **sudah memiliki halaman lengkap**. Dokumen ini hanya menambah dan menyesuaikan. Ikuti aturan kerja ini:

1. **Jangan menulis ulang** halaman, model, migration, atau Service yang sudah ada. Ubah sekecil mungkin. Jika sebuah file harus diubah, ubah bagian yang disebut saja.
2. **Audit dulu, baru kerja.** Sebelum kode apa pun, buat `docs/AUDIT_REPO.md` berisi: nama tabel dan kolom nyata untuk konsultasi, penawaran, detail penawaran, proyek, termin, itinerary draft; nama Controller, Service, Agent, Policy, route yang sudah ada; dan pemetaan setiap `CHG-xx` di bawah ke file nyata. Nama di dokumen ini adalah **nama logis** (mengikuti SRS). **Pakai nama nyata di repo.**
3. **Migration hanya menambah** (kolom baru nullable atau berbawaan, tabel baru). Jangan mengubah tipe, menghapus, atau mengganti nama kolom yang sudah ada. Setiap migration dapat naik dan turun.
4. **Fitur baru di balik konfigurasi** (`config/empirise.php`) bila berisiko, agar bisa dimatikan tanpa deploy ulang.
5. **Jika sebuah CHG sudah ada di repo**, tandai `SUDAH ADA` di audit dan lewati. Jangan membuat duplikat.
6. **Jangan mengarang.** Hal yang tidak jelas masuk `docs/OPEN_QUESTIONS.md`, ambil asumsi paling aman, tulis di deskripsi commit.
7. **Rencana dulu.** Untuk tiap tahap, tulis rencana singkat (file yang akan diubah, risiko), tunggu persetujuan pemilik proyek, baru implementasi.
8. **Tanpa Filament dan tanpa Livewire.** Seluruh admin (`/admin/*`) memakai Controller, Form Request, Policy, dan view Blade dengan Alpine.js. Jangan memasang `filament/*` atau `livewire/*`. Jika repo ternyata masih memakai Filament di sebagian halaman, **jangan menghapusnya sendiri**: catat di `AUDIT_REPO.md` dan tanyakan.
9. Aturan keras R1 sampai R15 di `AGENTS.md` **tetap berlaku**, kecuali yang diubah eksplisit di Bagian 2.

---

## 1. Ringkasan Perubahan

| ID | Perubahan | Prioritas | Dampak | Sifat |
|---|---|---|---|---|
| CHG-01 | Alur **cerita dulu**: AI membaca dan memahami cerita klien, baru merekomendasikan | P0 | AI, publik | Tambahan pada alur AI yang ada |
| CHG-02 | **Gerbang login**: konsultasi, rekomendasi, itinerary bebas tanpa login. Login hanya untuk Pesan dan Deal Harga, pemantauan, negosiasi | P0 | Auth, publik | Penyesuaian alur |
| CHG-03 | **Proposal lengkap** (bukan hanya itinerary) yang dibuat otomatis, admin tinggal menyesuaikan | P0 | Admin, penawaran | Perluasan tabel penawaran |
| CHG-04 | **Unduh PDF** proposal (formal, berfoto) | P0 | Penawaran | Fitur baru |
| CHG-05 | **Foto dan logo** pada proposal, plus **pratinjau langsung** di editor admin | P0 | Admin | Fitur baru |
| CHG-06 | **Negosiasi** lewat Minta Revisi (termasuk tawaran harga) dan versi baru yang menyalin isi proposal | P0 | Penawaran | Penyesuaian |
| CHG-07 | Tujuan **bebas** (apa pun, dalam atau luar negeri) | P1 | AI, data | Pelonggaran aturan R5 |
| CHG-08 | Segmen klien, hasil yang diinginkan, dan 3 layanan baru | P1 | Form, seeder | Tambahan |
| CHG-09 | Bagian **Keselamatan dan Operasional** di proposal | P1 | Proposal | Tambahan |
| CHG-10 | Bagian **Mengapa Empirise** dan tombol **WhatsApp** | P1 | Beranda | Tambahan konten, pelonggaran BR-13 |
| CHG-11 | Perbaikan modul admin lain (kalender kapasitas, pengguna, audit, laporan) | P1 | Admin | **Hanya yang belum ada** |
| CHG-12 | Pilihan penawaran berdampingan Hemat, Standar, Premium | P2 | Penawaran | Opsional, tahap akhir |

---

## 2. Perubahan Aturan pada Dokumen yang Ada

Perbarui kutipan berikut di SRS dan AGENTS.md pada commit yang sama dengan implementasi.

| Aturan lama | Aturan baru | Alasan |
|---|---|---|
| **R5 / BR-09**: AI hanya memakai destinasi dari database | Peringkat tetap dari TF-IDF atas database. **Tambahan:** bila cerita menyebut tujuan yang tidak ada di database, AI boleh mengusulkannya sebagai **tujuan bebas** (`sumber = bebas`). Tujuan bebas **tidak boleh** disertai jam buka, fasilitas, atau klaim ketersediaan. Admin melihat penanda "belum ada di database" | Owner: "apa pun perjalanan kami siap melayani" |
| **REQ-ITN-07**: setiap `id_destinasi` harus ada di kandidat | `id_destinasi` harus ada di kandidat **atau** null dengan `nama_bebas` terisi | Konsekuensi di atas |
| **AI-05**: katakan jujur bila di luar layanan | Bila kebutuhan tidak lazim, **diteruskan ke admin** (bukan ditolak). Hanya hal ilegal atau berbahaya yang ditolak | Keputusan pemilik |
| **BR-13 / R14**: tanpa WhatsApp | Tanpa **integrasi** WhatsApp (tanpa API, tanpa bot WA). **Boleh** tautan click-to-chat `https://wa.me/<nomor>` di beranda, hasil rekomendasi, dan proposal. Nomor di `config/empirise.php` | Owner menyebut WhatsApp sebagai kanal utama |
| **REQ-AKN-06 / FR-12** (tautan konsultasi ke akun) | Tetap. Tambahan: tindakan **tertunda** sebelum login (kirim itinerary, form Pesan) harus dilanjutkan otomatis setelah login atau daftar | CHG-02 |
| **REQ-PNW-02** (draf penawaran dari itinerary) | Diperluas menjadi **proposal lengkap** (CHG-03) | |
| **R2 AI tanpa harga** | **Tidak berubah.** Validator harus menolak keluaran yang memuat pola harga | |
| **Stack admin** (AGENTS.md Bagian 3, SDD Bagian 5.4, ADR-04) | Filament 5 dan Livewire 4 **dihapus dari stack**. Admin = Blade + Alpine + Tailwind, rute `/admin/<menu>`, middleware peran `admin` (mis. `EnsureRole`), Policy untuk tiap aksi, Form Request untuk validasi, komponen Blade bersama dengan situs publik (`x-button`, `x-card`, `x-status-badge`, `x-pill-filter`, `x-stat-card`). Ekspor tetap `maatwebsite/excel` | Keputusan pemilik; sesuai ADMIN_UI_SPEC opsi A |
| **R4 AI hanya draf** | **Tidak berubah.** AI tidak mengirim proposal. Admin yang mengirim | |

---

## 3. CHG-01: Alur Cerita Dulu (AI Memahami, Baru Merekomendasikan)

### 3.1 Perilaku wajib

1. Pengguna publik menulis cerita di (a) kotak cerita beranda, (b) `/rencana`, atau (c) Chatbot Publik. Tanpa login.
2. Sistem menyamarkan PII (`PiiMasker`, tidak berubah).
3. AI **membaca dan memahami dulu**: apa masalahnya, siapa kliennya, apa hasil yang diharapkan.
4. Antarmuka menampilkan **"Yang kami pahami dari cerita Anda"** (2 sampai 3 kalimat, bahasa klien) **sebelum** rekomendasi.
5. Baru kemudian: layanan yang cocok, tepat 3 destinasi dengan **alasan** yang mengaitkan ke cerita, itinerary 1 sampai 4 hari, maksimal 2 pertanyaan lanjutan.
6. Disclaimer tetap dan label **Gambaran awal** wajib (REQ-ITN-09).

### 3.2 Kontrak keluaran Agent (perluasan SRS 8.2)

Tambahkan kolom ke skema itinerary. Kolom lama tidak dihapus.

```ts
interface ItineraryV2 extends Itinerary {
  pemahaman_ringkas: string;        // 2-3 kalimat, bahasa klien (BARU)
  tipe_klien: 'korporat'|'kampus'|'instansi'|'umum'|null;
  layanan_disarankan: string[];     // dari master layanan
  destinasi: Array<{                // tepat 3
    id_destinasi: number | null;    // null bila tujuan bebas
    nama: string;
    sumber: 'database' | 'bebas';
    alasan: string;                 // satu kalimat, kaitkan ke cerita
    kecocokan: number;              // 50-98, dari TF-IDF bila database
  }>;
  pertanyaan_lanjutan: string[];    // maks 2
}
```

Simpan ke `itinerary_draft.itinerary_json` (kolom yang sudah ada). Tidak perlu tabel baru.

### 3.3 Validator keluaran (perluas yang ada)

Gagal bila: skema tidak lengkap; jumlah destinasi bukan 3; `sumber=database` tetapi id bukan kandidat; ada pola harga (`/Rp|rupiah|juta|ribu|diskon/i`); disclaimer tidak ada; `sumber=bebas` memuat kata jam buka atau fasilitas. Gagal: retry sekali, lalu **mode cadangan** (arahkan ke `/rekomendasi` dan `/konsultasi/baru`), sesuai REQ-ITN-07 dan R8.

### 3.4 Instruksi sistem Agent (taruh di `instructions()`)

```
Anda konsultan perjalanan dan kegiatan Empirise Jalan-Jalan. Baca dan pahami dulu
cerita calon klien dengan empati dan ketelitian: apa masalahnya, siapa mereka, apa
hasil yang diharapkan. Lalu rekomendasikan.
Aturan:
- Bahasa Indonesia yang hangat, singkat, sopan.
- Ringkas pemahaman Anda dengan bahasa klien sebelum merekomendasikan.
- Tepat 3 destinasi, itinerary 1 sampai 4 hari sesuai cerita.
- Gunakan kandidat dari konteks. Jika klien menyebut tujuan lain, boleh diusulkan
  sebagai tujuan bebas tanpa jam buka dan fasilitas.
- DILARANG menyebut harga, biaya, diskon, atau janji ketersediaan tanggal, tim,
  dan armada.
- Jangan mengarang jam buka atau fasilitas. Abaikan data pribadi.
- Tanyakan paling banyak 2 hal yang benar-benar menentukan.
- Layanan dipilih dari master layanan.
```

### 3.5 Antarmuka (tanpa merombak halaman yang ada)

- Beranda: sisipkan **satu section baru tepat di bawah hero**, sebelum section Layanan: judul "Ceritakan rencana atau keluhan Anda. Kami rekomendasikan wisata dan itinerary-nya", label "Fitur utama, gratis tanpa login", 3 langkah (Ceritakan, Rekomendasi dan itinerary, Pesan dan deal harga: masuk atau daftar), kolom cerita (maks 1500), 3 contoh cerita sekali klik, pilihan "Anda mewakili siapa" dan kolom "Hasil yang ingin dicapai" (CHG-08), tombol utama **Buat Rekomendasi dan Itinerary**, area hasil.
- Tombol hero utama menjadi "Ceritakan rencana Anda, gratis" dan menggulir ke section itu.
- Tombol chat melayang diberi label "Ceritakan rencana Anda".
- Hasil memuat urutan: **Yang kami pahami**, layanan, kartu destinasi (alasan, bilah kecocokan), itinerary, pertanyaan lanjutan, disclaimer, lalu tombol: **Pesan dan Deal Harga** (CHG-02), **Konsultasi gratis dengan admin**, **Ubah cerita**, **Diskusi via WhatsApp**.
- Di Chatbot Publik, alurnya sama: pesan pertama mengundang bercerita, setelah itu pesan "membaca cerita", lalu hasil.

---

## 4. CHG-02: Gerbang Login

| Tindakan | Perlu login? |
|---|---|
| Membaca halaman publik, bercerita, menerima rekomendasi dan itinerary | Tidak |
| Mengirim form **Konsultasi** (REQ-KON-03: nama, email, telepon) | **Tidak** |
| **Pesan dan Deal Harga**, form `/pesan`, tombol Kirim ke Empirise dari rekomendasi | **Ya** |
| Memantau konsultasi, melihat proposal, menyetujui, negosiasi, pembayaran, Asisten Akun | **Ya** |

Implementasi:

- Middleware `auth` hanya untuk rute di tabel yang bertanda Ya. **Jangan** menambah `auth` pada `/konsultasi/baru`.
- Simpan **tindakan tertunda** di sesi (`pending_action`: jenis + id itinerary atau payload form) lalu tampilkan halaman masuk atau daftar dengan catatan "Cerita dan rekomendasi Anda tersimpan dan akan dikirim setelah masuk". Setelah login atau registrasi, jalankan tindakan itu dan arahkan ke `/akun/konsultasi/{id}`.
- Itinerary draft publik sudah memakai `sesi_id`. Pastikan `id_klien` terisi saat ditautkan (REQ-AKN-06).
- Setelah konsultasi publik dikirim tanpa login, halaman sukses menjelaskan bahwa **memantau balasan dan negosiasi harga memerlukan masuk atau daftar**. Email konfirmasi memuat tautan ke akun (REQ-EML-01).
- Jangan bocorkan apakah sebuah email sudah terdaftar (pesan galat umum).

Uji (Pest): tamu bisa memanggil rekomendasi dan membuat konsultasi; tamu **tidak bisa** mengakses `/akun/*` dan `/pesan` (redirect ke masuk, tindakan tertunda tersimpan); setelah login, tindakan tertunda dijalankan tepat satu kali.

---

## 5. CHG-03: Proposal Lengkap

Proposal adalah **penawaran yang diperkaya**, bukan entitas baru. Tetap satu penawaran per versi, versi lama tidak berubah (R12).

### 5.1 Migration (hanya menambah)

Tabel `penawaran` (nama nyata sesuai audit), tambah kolom:

| Kolom | Tipe | Isi |
|---|---|---|
| `judul` | VARCHAR(200) NULL | Judul proposal |
| `pemahaman` | TEXT NULL | Pemahaman kebutuhan (dari AI, boleh diedit) |
| `cakupan_termasuk` | TEXT NULL | Satu butir per baris |
| `cakupan_tidak` | TEXT NULL | Satu butir per baris |
| `keselamatan` | TEXT NULL | Lihat CHG-09 |
| `syarat` | TEXT NULL | Syarat dan ketentuan |
| `berlaku_hari` | SMALLINT UNSIGNED DEFAULT 14 | Masa berlaku |
| `logo_path` | VARCHAR(255) NULL | Logo (disk `public`) |

Tabel baru `penawaran_hari`: `id`, `id_penawaran` FK, `urutan` TINYINT, `judul` VARCHAR(60), `isi` TEXT (satu kegiatan per baris, format `HH:MM aktivitas`).

Tabel baru `penawaran_foto`: `id`, `id_penawaran` FK, `jenis` ENUM(`sampul`,`destinasi`,`galeri`), `nama_destinasi` VARCHAR(150) NULL, `path` VARCHAR(255), `keterangan` VARCHAR(200) NULL, `urutan` TINYINT.

Tabel `detail_penawaran` **tidak diubah**. Baris biaya tetap di sana.

### 5.2 Pembuatan otomatis

`PenawaranService::buatProposalDariKonsultasi(Konsultasi $k)` (perluas `buatDraf...` yang ada, jangan menggantinya):

1. Ambil itinerary tertaut (`id_itinerary`). Jika tidak ada, isi minimum dari data konsultasi.
2. `judul` = layanan + organisasi + `(N hari)`.
3. `pemahaman` = `pemahaman_ringkas` dari itinerary, atau ringkasan cerita.
4. `penawaran_hari` dari `itinerary_json.hari[]` (jam + aktivitas).
5. `detail_penawaran` dari `kebutuhan_pendukung` dan `layanan_pendukung`, **ditambah baris standar** dari tabel `harga_default` (REQ-PNW-13): transportasi, akomodasi (bila lebih dari 1 hari: peserta x (hari-1) orang-malam), konsumsi (peserta x hari), fasilitator dan kegiatan, tiket dan kunjungan, dokumentasi, asuransi, koordinasi lapangan. Harga dari `harga_default`, **kosong bila tidak ada default** (REQ-PNW-03 tetap menolak kirim bila ada harga kosong).
6. `cakupan_termasuk`, `cakupan_tidak`, `syarat`: teks bawaan dari `config/empirise.php` (lihat Lampiran A), dapat diedit.
7. `keselamatan`: lihat CHG-09.
8. Foto: tidak ada yang wajib. Bila destinasi database punya foto, salin referensinya sebagai `penawaran_foto` jenis `destinasi`.

### 5.3 Editor admin

Tambahkan ke layar penawaran admin yang ada (jangan mengganti layar lama). Dibangun dengan **Blade + Alpine, bukan Filament**: baris biaya dan rencana per hari dikelola Alpine (tambah, hapus, total langsung), simpan lewat Form Request biasa: tombol **Buat Proposal Lengkap (otomatis)** menggantikan **Buat Penawaran dari Itinerary** atau memanggil service yang sama. Editor berisi bagian: judul; pemahaman; rencana per hari (satu kolom teks per hari); termasuk dan tidak termasuk; keselamatan; syarat dan masa berlaku; foto (CHG-05); rincian biaya (baris dapat ditambah, diubah, dihapus; total dihitung langsung); tombol **Kirim Proposal Lengkap ke Klien**. Kirim memakai alur penawaran yang ada (status `dikirim`, email REQ-EML-06, atau ubah subjek menjadi "Proposal kegiatan Anda (versi {versi})" lewat `email_template`).

### 5.4 Tampilan klien

Di `/akun/.../penawaran/{id}` tampilkan, berurutan: logo, foto sampul, judul, 1 Pemahaman kebutuhan, foto destinasi, 2 Rencana kegiatan, galeri, 3 Sudah termasuk, 4 Belum termasuk, keselamatan, 5 Pembayaran (termin 30/40/30 dihitung dari total), 6 Syarat dan ketentuan, lalu tabel **Rincian biaya** dan tombol **Setuju**, **Negosiasi / Minta Revisi**, **Tolak**, serta **Unduh PDF**. Alur Setuju (R6, R7) tidak berubah.

Policy: klien hanya melihat penawaran miliknya yang berstatus selain `draft` (R1, 404 untuk lainnya).

---

## 6. CHG-04: Unduh PDF

### 6.1 Pilihan teknis

- Pakai **`barryvdh/laravel-dompdf`** (PHP murni, tanpa Chrome). **Verifikasi kompatibilitas dengan Laravel 13** di `composer`. Bila tidak kompatibel, pilih alternatif (misalnya `spatie/laravel-pdf`) dan **catat alasan di commit dan `SYSTEM_ARCHITECTURE.md` Bagian 15**. Ini pengecualian atas aturan "jangan menambah paket baru" di `AGENTS.md` Bagian 3. Perbarui tabel stack di sana.
- Satu **view Blade tunggal** `pdf/proposal.blade.php` dipakai untuk PDF **dan** pratinjau (CHG-05). Jangan membuat dua template.

### 6.2 Endpoint

| Method | Path | Auth | Fungsi |
|---|---|---|---|
| GET | `/akun/penawaran/{id}/pdf` | Klien pemilik | Unduh PDF versi itu (404 bila bukan miliknya, atau masih `draft`) |
| GET | `/admin/penawaran/{id}/pdf` | Admin | Unduh PDF, termasuk draf |

Nama berkas: `Proposal-{kode_konsultasi}-V{versi}.pdf`. Setiap unduhan dicatat ke `audit_log` bila tabel itu ada.

### 6.3 Tata letak (A4, formal, aksen merah brand)

Warna `#D32F2F`, teks `#1A1A1A`, abu `#6B6B6B`, pita gelap `#0A0A0A`. Font Poppins (pasang ke dompdf) atau fallback sans-serif.

| Halaman | Isi |
|---|---|
| **Sampul** | Foto sampul penuh lebar (tinggi sekitar 47%), logo di kotak putih pojok kiri atas, garis merah, label "PROPOSAL KEGIATAN", judul besar, "Disusun untuk" nama dan organisasi, `Nomor {kode}/V{versi} | {tanggal} | Berlaku {n} hari`, pita gelap di dasar bertuliskan EMPIRISE JALAN-JALAN dan daftar layanan |
| **Isi** (urut, **penomoran otomatis**, bagian kosong dilewati) | Pemahaman Kebutuhan (plus ringkasan layanan, peserta, durasi, tujuan); Destinasi dan Dokumentasi (grid 2 kolom, 3:2, keterangan, "(ilustrasi)" bila bukan foto asli); Galeri Dokumentasi; Rencana Kegiatan (pita per hari, jam merah); Cakupan Layanan; Keselamatan dan Operasional; Rincian Biaya (tabel: item, jumlah, harga satuan, subtotal; baris **TOTAL INVESTASI** gelap); Jadwal Pembayaran (3 termin dengan nominal); Syarat dan Ketentuan; Pengesahan (dua kotak tanda tangan: Empirise dan klien) |
| **Footer** (halaman 2 dst) | Garis tipis, `Empirise Jalan-Jalan | {email admin} | {kode} V{versi}`, `Halaman x / y`, logo kecil di kanan atas |

Aturan: tabel dan blok foto tidak terpotong di tengah halaman (`page-break-inside: avoid`); teks Rupiah `Rp 1.500.000`; tanggal `dd MMMM yyyy`; **tidak ada harga dari AI**, hanya angka dari `detail_penawaran`.

---

## 7. CHG-05: Foto, Logo, dan Pratinjau Langsung

### 7.1 Unggah (admin, di editor penawaran)

| Slot | Aturan |
|---|---|
| **Logo** | PNG atau JPG, maks 2 MB, diperkecil maks 500 px lebar, **PNG dipertahankan** (transparansi) |
| **Foto sampul** | Terpisah dari foto destinasi. Dipangkas ke rasio 3:2 (1200x800), JPEG kualitas 85 |
| **Foto destinasi** | Satu slot per destinasi di rencana. Perlakuan sama dengan sampul |
| **Foto lainnya** | Tim, armada, fasilitas, dokumentasi lama. Maks 6 per unggahan, ada kolom keterangan dan tombol hapus |

Keamanan (NFR-S06): hanya `jpg`, `png`, `webp`, maks 2 MB, nama acak, `mimetypes` diverifikasi di server (bukan hanya ekstensi), simpan di disk `public` terkontrol, hapus berkas bila baris dihapus. Gunakan GD yang sudah ada untuk pangkas dan perkecil. Bila destinasi tidak punya foto, tampilkan penampung netral dengan keterangan "(ilustrasi)", **jangan** memakai gambar dari sumber tanpa izin (AGENTS.md Bagian 12).

### 7.2 Pratinjau langsung

- Di layar editor: dua kolom. Kiri editor, kanan pratinjau **menempel saat menggulir**.
- Pratinjau dirender **server** dari view yang sama dengan PDF: `POST /admin/penawaran/{id}/pratinjau` menerima isi formulir (belum disimpan), mengembalikan **HTML parsial** halaman-halaman.
- Alpine memanggil endpoint itu dengan **debounce 300 ms** setiap perubahan teks, harga, atau foto. Tampilkan indikator memuat. Bila gagal, pertahankan pratinjau terakhir.
- Tombol **Unduh PDF** dan tombol **PDF asli** (menampilkan keluaran dompdf sungguhan di dalam bingkai). Pembagian halaman pada pratinjau HTML adalah **perkiraan**, PDF asli yang menjadi acuan.
- Teks pada pratinjau di-escape (R10). Hanya admin yang boleh memanggil endpoint ini.

---

## 8. CHG-06: Negosiasi dan Versi

- Ubah label tombol klien **Minta Revisi** menjadi **Negosiasi / Minta Revisi**. Catatan tetap wajib (REQ-PNW-06). Placeholder: "Catatan revisi atau tawaran harga Anda". **Perilaku tidak berubah**: status penawaran menjadi `revisi`, konsultasi kembali ke `dibalas`, admin mendapat notifikasi.
- Admin melihat kartu **"Klien meminta revisi atau negosiasi harga"** berisi catatan, dengan tombol **Buat Versi {n+1}**.
- Aksi itu `PenawaranService::buatVersiBaru`: **salin seluruh isi proposal** (kolom CHG-03, hari, foto, baris biaya) ke baris versi baru berstatus `draft`. Versi lama tidak berubah (R12). Admin menyesuaikan lalu mengirim ulang.
- Negosiasi juga dapat berlangsung di **chat konsultasi** (REQ-KON-06, sudah ada). Asisten Akun **tidak boleh** menawar atau menjanjikan diskon (REQ-CBA-07), hanya menyusun **draf** catatan revisi yang dikirim klien lewat tombol (R4).

---

## 9. CHG-07, CHG-08, CHG-09: Data dan Aturan Pendukung

### 9.1 CHG-07 Tujuan bebas
- Form Pesan: tambahkan kolom teks **"Tujuan lain, ke mana pun (pisahkan dengan koma)"** di samping pilihan destinasi. Validasi: wajib minimal satu tujuan, dari pilihan atau teks. Simpan ke kolom tujuan yang sudah ada atau ke `preferensi_json`.
- Admin: pada proposal, tujuan bebas ditandai **"belum ada di database"** dengan aksi cepat **Tambahkan ke Destinasi** (membuat destinasi nonaktif untuk dilengkapi tag, deskripsi, foto, lalu hitung ulang TF-IDF).
- Daftar bawaan seeder (CONTOH, tandai jelas): Bali, Labuan Bajo, Bromo, Lombok, Bandung, Singapura, Jepang, selain destinasi yang sudah ada.

### 9.2 CHG-08 Segmen, hasil yang diinginkan, layanan baru
- Kolom baru di `konsultasi` (nullable): `tipe_klien` ENUM(`korporat`,`kampus`,`instansi`,`umum`), `hasil_diinginkan` TEXT. `organisasi` dan `rentang_budget` sudah ada di SRS 6.2, lewati bila sudah ada.
- Master layanan (seeder, idempoten): tambahkan **Kunjungan Industri & Edukasi**, **Pelatihan & Pengembangan SDM**, **Hiburan & Awarding**. Layanan lama tidak diubah.
- Segmen mempengaruhi saran: korporat (gathering, outbound, pelatihan SDM), kampus (kunjungan industri, wisata edukasi), instansi (perjalanan dinas, kunjungan, event). Cukup sebagai konteks Agent, **bukan** logika harga.

### 9.3 CHG-09 Keselamatan dan Operasional
Teks bawaan otomatis (dapat diedit admin), dipilih dengan pencocokan kata pada tujuan dan cerita. Taruh aturannya di `config/empirise.php` agar mudah diubah:

| Selalu ada | Briefing keselamatan sebelum kegiatan; kotak P3K dan petugas pendamping; asuransi perjalanan untuk seluruh peserta |
|---|---|
| Pantai, laut, pulau, kapal | Petugas khusus memantau kondisi ombak dan air laut, termasuk saat makan malam di pantai; kegiatan disesuaikan dengan cuaca dan dapat dialihkan ke lokasi aman |
| Gunung, outbound, adventure | Pemandu berpengalaman dan peralatan pengaman untuk outbound dan medan terbuka |
| Kunjungan, industri, kampus | Kesediaan instansi tujuan dikonfirmasi lebih dulu; alternatif lokasi disiapkan bila kunjungan tidak dapat diterima |

---

## 10. CHG-10: Mengapa Empirise dan WhatsApp

- Beranda: tambahkan satu section **Mengapa Empirise** (setelah kotak cerita, sebelum Layanan). Judul memakai pesan utama owner: "Cukup sampaikan tujuan, kebutuhan, dan harapan Anda. Kami mengubahnya menjadi kegiatan yang terencana, nyaman, profesional, dan berkesan." Enam kartu: Solusi bukan sekadar paket; Fleksibel dan custom; Legalitas jelas; Pengalaman terbukti; Tim lengkap; Keselamatan diutamakan. Ambil data dari tabel yang ada (layanan, tim, portofolio, klien) bila ada, **jangan memajang data legalitas atau angka yang belum diverifikasi**. Pakai penanda `CONTOH` di seeder dan catat di `OPEN_QUESTIONS.md`.
- WhatsApp: `config/empirise.php` → `whatsapp_number` (format internasional tanpa `+`). Tombol `https://wa.me/{nomor}?text={URL-encoded}` dengan `target="_blank" rel="noopener"`. Tempat: section Mengapa Empirise, hasil rekomendasi, footer atau halaman FAQ dan Kontak (TBD-10). Tanpa API, tanpa webhook, tanpa penyimpanan percakapan WhatsApp.

---

## 11. CHG-11: Modul Admin Lain (Hanya Jika Belum Ada)

Audit terhadap `docs/ADMIN_UI_SPEC.md`. Semua halaman ditulis sebagai **Blade + Alpine**, tanpa Filament (CRUD konten memakai Controller resource biasa, tabel dan filter memakai komponen Blade sendiri). Untuk setiap item berikut, tandai `SUDAH ADA` atau kerjakan:

| Item | Ringkas |
|---|---|
| Kalender dengan **penanda kapasitas** | Tabel `penanda_kapasitas` (`tanggal`, `jenis` tim atau armada, UNIQUE bersama); status tanggal: penuh bila ada penanda, terbatas bila ada kegiatan, tersedia bila tidak ada. Dipakai kalender publik dan Tool `cek_status_tanggal` |
| Modul **Pengguna** | Ubah peran dengan konfirmasi; admin tidak bisa mengubah dirinya; minimal satu admin |
| **Audit log** | `audit_log` (`user_id`, `aksi`, `objek`, `sebelum`, `sesudah`, `created_at`) untuk ubah peran, unduh laporan, kapasitas, kirim penawaran, unduh PDF |
| **Laporan** | Rekap Konsultasi, Proyek, Pembayaran; ekspor CSV dan Excel dengan nominal angka murni |
| Dashboard | Kartu Konsultasi baru, Perlu ditanggapi, Penawaran menunggu, **Pembayaran jatuh tempo**, Proyek berjalan; tambah funnel konversi (konsultasi, penawaran, deal) |
| Chatbot AI (admin) | Tab FAQ chatbot, Log percakapan (termasuk yang diblokir karena harga), Rekomendasi (uji, ambang skor, jumlah hasil) |
| Konten Web | Tab Tim, Layanan, Portofolio, Destinasi, FAQ, **Klien**, **Testimoni**; nonaktifkan, bukan hapus, bila sudah dipakai data lain |

---

## 12. CHG-12 (P2, Tahap Akhir): Pilihan Penawaran Berdampingan

Owner: bandingkan penawaran harus **setara** (cakupan sama). Usulan, kerjakan hanya setelah semua P0 dan P1 lulus: satu konsultasi dapat memiliki beberapa **opsi** (Hemat, Standar, Premium) dalam satu versi; klien melihat **tabel perbandingan** (baris cakupan, kolom opsi) dan memilih satu saat Setuju. Perlu tabel `penawaran_opsi` dan perubahan alur deal. **Tanyakan persetujuan sebelum memulai.**

---

## 13. Rute Baru (Ringkas)

| Method | Path | Auth | Fungsi |
|---|---|---|---|
| GET | `/akun/penawaran/{id}/pdf` | Klien | Unduh PDF |
| GET | `/admin/penawaran/{id}/pdf` | Admin | Unduh PDF |
| POST | `/admin/penawaran/{id}/pratinjau` | Admin | HTML pratinjau dari formulir |
| POST | `/admin/penawaran/{id}/foto` | Admin | Unggah logo, sampul, destinasi, galeri |
| DELETE | `/admin/penawaran/{id}/foto/{fid}` | Admin | Hapus foto |
| POST | `/admin/penawaran/{id}/versi-baru` | Admin | Salin ke versi berikutnya |

Endpoint AI yang ada (`/api/ai/*`, `/api/chat/*`) **dipertahankan**. Hanya bentuk respons yang diperluas (Bagian 3.2). Klien lama yang membaca kolom lama harus tetap berfungsi.

---

## 14. Tes yang Wajib (Pest)

| Tes | Memverifikasi |
|---|---|
| `AiRekomendasiTest` | Pemahaman muncul dan tidak kosong; tepat 3 destinasi; tanpa pola harga (R2); id database di kandidat; tujuan bebas ditandai; mode cadangan saat AI gagal (R8); fake SDK, **tanpa panggilan API nyata** |
| `PiiTest` | Tidak ada nama, email, telepon yang terkirim ke SDK (R3) |
| `GerbangLoginTest` | Tamu boleh rekomendasi dan konsultasi; tamu ditolak di `/pesan` dan `/akun/*`; tindakan tertunda berjalan sekali setelah login |
| `ProposalGenerasiTest` | Proposal lengkap terbentuk dari itinerary; harga kosong menolak kirim; versi lama tidak berubah |
| `NegosiasiVersiTest` | Revisi membuat status `revisi`; versi baru menyalin seluruh isi |
| `ProposalPdfTest` | PDF terunduh, tipe `application/pdf`; klien lain mendapat 404; klien tidak bisa mengunduh `draft` |
| `UnggahFotoTest` | Menolak berkas non-gambar, lebih dari 2 MB, MIME palsu; nama acak; berkas terhapus saat baris dihapus |
| `PratinjauTest` | Endpoint hanya admin; keluaran di-escape; tidak menyimpan data |
| `SetujuPenawaranTest` | **Tetap lulus** (R6 dan R7 tidak berubah) |
| `IsolasiDataTest` | **Tetap lulus**, termasuk lewat chatbot (R1) |

---

## 15. Urutan Kerja dan Prompt untuk Antigravity

Kerjakan **berurutan**. Setelah tiap tahap: `vendor/bin/pint`, `vendor/bin/pest`, `npm run build`, lalu commit dengan ID `CHG-xx`.

**Tahap 0: Audit.**
```
Baca docs/CHANGE_SPEC.md Bagian 0 dan seluruh docs/ lainnya. Jangan mengubah kode.
Buat docs/AUDIT_REPO.md: nama tabel, kolom, model, Controller, Service, Agent, Policy,
dan route nyata; pemetaan tiap CHG-01 sampai CHG-12 ke file nyata; tandai SUDAH ADA,
SEBAGIAN, atau BELUM. Daftar risiko dan pertanyaan masuk docs/OPEN_QUESTIONS.md.
```

**Tahap 1: Data (CHG-03 skema, CHG-08).**
```
Sesuai CHANGE_SPEC Bagian 5.1 dan 9.2: buat migration tambahan saja (kolom nullable,
dua tabel baru), seeder layanan baru idempoten, perbarui model dan $fillable. Jangan
ubah kolom yang ada. Tes: migrate naik dan turun, seeder dua kali tidak menggandakan.
```

**Tahap 2: Alur AI (CHG-01, CHG-07).**
```
Sesuai Bagian 3 dan 9.1: perluas keluaran ItineraryGenerator dan ItineraryAgent ke
skema V2, tambahkan pemahaman_ringkas dan alasan per destinasi, dukung tujuan bebas,
perluas validator, pertahankan mode cadangan. Tambahkan section kotak cerita di
beranda dan arahkan Chatbot Publik ke alur yang sama. Jangan mengubah PiiMasker dan
RecommendationService. Tes AiRekomendasiTest dan PiiTest dengan fake SDK.
```

**Tahap 3: Gerbang login (CHG-02).**
```
Sesuai Bagian 4: tindakan tertunda di sesi, middleware auth hanya untuk rute bertanda Ya,
/konsultasi/baru tetap publik. Tes GerbangLoginTest. Jangan mengubah Breeze.
```

**Tahap 4: Proposal lengkap dan negosiasi (CHG-03, CHG-06).**
```
Sesuai Bagian 5 dan 8: PenawaranService::buatProposalDariKonsultasi dan buatVersiBaru,
editor admin, tampilan klien, label Negosiasi / Minta Revisi. Admin dengan Blade + Alpine (tanpa Filament). Jangan mengubah alur
setujui (R6/R7). Tes ProposalGenerasiTest, NegosiasiVersiTest, SetujuPenawaranTest.
```

**Tahap 5: Foto, logo, pratinjau, PDF (CHG-04, CHG-05, CHG-09).**
```
Sesuai Bagian 6, 7, dan 9.3: pasang dompdf (verifikasi Laravel 13, catat keputusan),
satu view pdf/proposal untuk PDF dan pratinjau, unggah dan hapus foto aman, endpoint
pratinjau dengan debounce Alpine, bagian Keselamatan dari config. Tes ProposalPdfTest,
UnggahFotoTest, PratinjauTest.
```

**Tahap 6: Konten, WhatsApp, modul admin sisa (CHG-10, CHG-11).**
```
Sesuai Bagian 10 dan 11: section Mengapa Empirise dan tombol wa.me dari config; kerjakan
hanya item CHG-11 yang belum ada menurut AUDIT_REPO.md. Perbarui SRS (BR-13) dan AGENTS.md
(R5, R14) sesuai Bagian 2.
```

**Tahap 7 (P2, tanya dulu): CHG-12.**

---

## 16. Definisi Selesai

- [ ] `composer.json` tidak memuat `filament/*` maupun `livewire/*`; tidak ada rute atau view admin yang bergantung padanya.
- [ ] Semua tes di Bagian 14 lulus, termasuk `SetujuPenawaranTest` dan `IsolasiDataTest` yang lama.
- [ ] `pint`, `pest`, `npm run build` bersih. Tidak ada `dd()`, `dump()`, atau rahasia tertinggal.
- [ ] Tamu dapat: bercerita, melihat pemahaman AI, rekomendasi, itinerary, dan mengirim konsultasi **tanpa login**. Pesan dan Deal Harga memicu login lalu melanjutkan otomatis.
- [ ] Admin membuat proposal lengkap otomatis, menyesuaikan, melihat **pratinjau langsung**, mengunggah logo dan foto, lalu mengirim.
- [ ] Klien (login) melihat proposal, mengunduh **PDF**, menyetujui atau bernegosiasi. Revisi membuat versi baru yang menyalin isi.
- [ ] Tidak ada harga dari AI, tidak ada PII ke penyedia AI, klien A tidak dapat melihat data klien B.
- [ ] SRS dan AGENTS.md diperbarui sesuai Bagian 2. `OPEN_QUESTIONS.md` terisi.
- [ ] Tampilan baru diuji pada 1489, 1280, 1024, 768, dan 390 px tanpa scroll horizontal.

---

## Lampiran A: Teks Bawaan (taruh di `config/empirise.php`, dapat diedit admin per proposal)

**Sudah termasuk:** Transportasi sesuai itinerary; Konsumsi sesuai program; Fasilitator dan pendamping tim Empirise; Tiket dan kunjungan sesuai itinerary; Dokumentasi foto kegiatan; Asuransi perjalanan.

**Belum termasuk:** Pengeluaran pribadi peserta; Biaya di luar program; Perubahan jadwal dari pihak peserta.

**Syarat dan ketentuan:** Harga berlaku selama masa berlaku proposal; Perubahan jumlah peserta dapat memengaruhi harga; Ketentuan pembatalan disepakati saat deal; Ketersediaan final dikonfirmasi tim Empirise.

**Pengesahan (kaki PDF):** "Persetujuan resmi dilakukan klien melalui tombol Setuju di akun Empirise. Dokumen ini bukan faktur."

**Baris biaya bawaan (hanya nama, satuan, rumus jumlah).** Nominal **harus** dari `harga_default`, jangan ditulis di kode:

| Item | Satuan | Jumlah |
|---|---|---|
| Transportasi | paket | 1 |
| Akomodasi (bila lebih dari 1 hari) | orang-malam | peserta x (hari - 1) |
| Konsumsi | orang-hari | peserta x hari |
| Fasilitator dan kegiatan | paket | 1 |
| Tiket dan kunjungan | orang | peserta |
| Dokumentasi | paket | 1 |
| Asuransi perjalanan | orang | peserta |
| Koordinasi tim lapangan | paket | 1 |

## Lampiran B: Hal yang Perlu Dikonfirmasi ke Owner (masuk `OPEN_QUESTIONS.md`)

1. Nomor WhatsApp resmi dan penempatannya (TBD-10).
2. Dokumen legalitas yang boleh ditampilkan (NIB, izin usaha, sertifikasi) dan data klien atau proyek yang boleh dipajang. Angka dan nama dari wawancara (misalnya nilai proyek besar, nama klien) **harus diverifikasi** sebelum dipakai.
3. Daftar harga default per item (`harga_default`) dan skema termin standar (bawaan 30/40/30).
4. Apakah tujuan di luar daftar boleh langsung dimasukkan ke proposal, atau perlu persetujuan admin dulu.
5. Perlu atau tidak pilihan penawaran berdampingan (CHG-12).
6. Masa berlaku proposal bawaan (usulan 14 hari) dan ketentuan pembatalan.
