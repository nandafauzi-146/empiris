# AUDIT REPOSITORI EMPIRISE JALAN-JALAN
**Tanggal Audit:** 24 November 2026  
**Dokumen Rujukan:** `docs/CHANGE_SPEC.md` v1.2, `AGENTS.md`, `docs/SRS.md`, `docs/SDD.md`

---

## 1. Ringkasan Eksekutif
Audit ini dilakukan secara menyeluruh terhadap skema basis data, arsitektur kode, rute, kontroler, service, dan status implementasi terhadap spesifikasi perubahan `CHG-01` sampai `CHG-12`.

Hasil penting:
1. **Admin Stack:** Terverifikasi **100% Blade + Alpine.js + Tailwind CSS**. Tidak ada dependensi Filament maupun Livewire sama sekali di `composer.json` atau kode sumber.
2. **Kondisi Pengujian:** 171/171 tes Pest lulus tanpa kegagalan.
3. **AI Chatbot:** Berjalan dengan Google Gemini API (`gemini-flash-lite-latest` dengan fallback `gemini-2.5-flash-lite`), tanpa akses ke database (R2 terjaga, 0 storage overhead).

---

## 2. Skema Tabel & Kolom Nyata

Semua tabel menggunakan penamaan bahasa Indonesia sesuai analisis awal:

### 2.1 `konsultasi`
- `id_konsultasi` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `id_klien` (BIGINT UNSIGNED, FK -> `users.id`, NULLABLE)
- `nomor_konsultasi` (VARCHAR, UNIQUE)
- `nama` (VARCHAR)
- `email` (VARCHAR)
- `telepon` (VARCHAR(30))
- `instansi` (VARCHAR, NULLABLE)
- `rentang_budget` (VARCHAR(24), NULLABLE)
- `layanan` (VARCHAR)
- `tujuan` (VARCHAR, NULLABLE)
- `jumlah_peserta` (INT UNSIGNED, NULLABLE)
- `durasi_hari` (SMALLINT UNSIGNED, NULLABLE)
- `tanggal_rencana` (DATE, NULLABLE)
- `minat` (TEXT, NULLABLE)
- `cerita_kebutuhan` (TEXT, NULLABLE)
- `jenis_kendaraan` (VARCHAR(30), NULLABLE)
- `lokasi_jemput` (VARCHAR, NULLABLE)
- `jam_jemput` (TIME, NULLABLE)
- `tanggal_kembali` (DATE, NULLABLE)
- `status` (VARCHAR(32), DEFAULT 'baru')
- `created_at`, `updated_at`
*Kebutuhan CHG-08:* Menambahkan kolom `tipe_klien` (VARCHAR(20), NULLABLE) dan `hasil_diinginkan` (TEXT, NULLABLE).

### 2.2 `penawaran`
- `id_penawaran` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `id_konsultasi` (BIGINT UNSIGNED, FK -> `konsultasi.id_konsultasi`)
- `versi` (SMALLINT UNSIGNED, DEFAULT 1)
- `status` (VARCHAR(20), DEFAULT 'draft')
- `judul` (VARCHAR(190))
- `catatan_admin` (TEXT, NULLABLE)
- `catatan_klien` (TEXT, NULLABLE)
- `total` (BIGINT UNSIGNED, DEFAULT 0)
- `dikirim_at` (TIMESTAMP, NULLABLE)
- `ditanggapi_at` (TIMESTAMP, NULLABLE)
- `disetujui_at` (TIMESTAMP, NULLABLE)
- `created_at`, `updated_at`
*Kebutuhan CHG-03:* Menambahkan `pemahaman` (TEXT, NULLABLE), `cakupan_termasuk` (TEXT, NULLABLE), `cakupan_tidak` (TEXT, NULLABLE), `keselamatan` (TEXT, NULLABLE), `syarat` (TEXT, NULLABLE), `berlaku_hari` (SMALLINT UNSIGNED, DEFAULT 14), `logo_path` (VARCHAR(255), NULLABLE).

### 2.3 `detail_penawaran`
- `id_detail` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `id_penawaran` (BIGINT UNSIGNED, FK -> `penawaran.id_penawaran`)
- `urutan` (SMALLINT UNSIGNED, DEFAULT 1)
- `nama_item` (VARCHAR(190))
- `deskripsi` (TEXT, NULLABLE)
- `jumlah` (DECIMAL(10,2), DEFAULT 1)
- `satuan` (VARCHAR(30), DEFAULT 'paket')
- `harga_satuan` (BIGINT UNSIGNED, NULLABLE)
- `asal` (VARCHAR(20), DEFAULT 'manual')
- `created_at`, `updated_at`

### 2.4 `proyek`
- `id_proyek` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `nomor_proyek` (VARCHAR, UNIQUE, NULLABLE)
- `id_konsultasi` (BIGINT UNSIGNED, FK -> `konsultasi.id_konsultasi`, UNIQUE)
- `id_penawaran` (BIGINT UNSIGNED, FK -> `penawaran.id_penawaran`, UNIQUE, NULLABLE)
- `id_klien` (BIGINT UNSIGNED, FK -> `users.id`)
- `nama` (VARCHAR(190))
- `tgl_mulai` (DATE)
- `tgl_selesai` (DATE)
- `status` (VARCHAR(20), DEFAULT 'terjadwal')
- `total_nilai` (BIGINT UNSIGNED)
- `catatan` (TEXT, NULLABLE)
- `created_at`, `updated_at`

### 2.5 `termin_pembayaran`
- `id_termin` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `id_proyek` (BIGINT UNSIGNED, FK -> `proyek.id_proyek`)
- `urutan` (TINYINT UNSIGNED)
- `nama` (VARCHAR(120))
- `nominal` (BIGINT UNSIGNED)
- `jatuh_tempo` (DATE)
- `status` (VARCHAR(20), DEFAULT 'belum_bayar')
- `jumlah_dibayar` (BIGINT UNSIGNED, DEFAULT 0)
- `tgl_bayar` (DATE, NULLABLE)
- `bukti_path` (VARCHAR, NULLABLE)
- `created_at`, `updated_at`

### 2.6 `penanda_kapasitas`
- `id_penanda` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `tanggal` (DATE)
- `jenis` (VARCHAR(20))
- `dibuat_oleh` (BIGINT UNSIGNED, FK -> `users.id`)
- `created_at`, `updated_at`

### 2.7 `konten_admin`
- `id_konten` (BIGINT UNSIGNED, PK, AUTO_INCREMENT)
- `jenis` (VARCHAR(32)) - destinasi, layanan, portofolio, klien, testimoni, faq
- `judul` (VARCHAR(190))
- `slug` (VARCHAR, NULLABLE)
- `deskripsi` (TEXT, NULLABLE)
- `metadata` (JSON, NULLABLE)
- `status` (VARCHAR(20), DEFAULT 'tampil')
- `aktif` (BOOLEAN, DEFAULT TRUE)
- `urutan` (INT UNSIGNED, DEFAULT 0)
- `created_at`, `updated_at`

### 2.8 Tabel Pendukung Lainnya
- `users`: `id`, `name`, `email`, `phone`, `role` (admin/klien), `aktif`, `password`, timestamps.
- `balasan_konsultasi`: `id_balasan`, `id_konsultasi`, `id_user`, `pesan`, `dibaca_at`, timestamps.
- `audit_log`: `id_audit`, `id_user`, `aksi`, `objek`, `id_objek`, `sebelum`, `sesudah`, timestamps.
- `rekomendasi_log`: `id_log`, `preferensi_json`, `hasil_json`, `latensi_ms`, timestamps.
- `email_log`: `id_log`, `id_konsultasi`, `jenis`, `referensi`, `ke_email`, `status`, `dikirim_at`, `created_at`.
- `pengaturan_bisnis`: `kunci` (PK), `nilai`, timestamps.

---

## 3. Rute & Kontroler Nyata

### 3.1 Publik
- `GET /` -> `welcome.blade.php` (`home`)
- `GET /tentang` -> `pages.about` (`about`)
- `GET /layanan` & `GET /layanan/{slug}` -> `pages.services.*`
- `GET /destinasi` & `GET /destinasi/{slug}` -> `DestinationController`
- `GET /paket/{slug}` -> `pages.packages.show`
- `GET /armada` -> `pages.vehicles.index`
- `GET /portofolio` & `GET /portofolio/{slug}` -> `pages.portfolios.*`
- `GET /klien` -> `pages.clients.index`
- `GET /konsultasi/baru` & `POST /konsultasi` -> `Public\ConsultationController`
- `GET /kalender` -> `PublicCalendarController`
- `POST /api/chatbot` -> `ChatbotPublicController`

### 3.2 Akun Klien (`/akun/*`)
- Middleware: `['auth', 'verified', 'role:klien']`
- `GET /akun/profil` & `PATCH /akun/profil` -> `ProfileController`
- `GET /akun/proyek` -> inline view `account.projects.index`
- `GET /akun/konsultasi` & `GET /akun/konsultasi/{id}` -> `Account\ConsultationController`
- `POST /akun/konsultasi/{id}/batal` -> `Account\ConsultationController@cancel`
- `POST /akun/konsultasi/{id}/balasan` -> `ConsultationReplyController@store`
- `POST /api/penawaran/{offer}/respons` -> `OfferResponseController@store`

### 3.3 Admin (`/admin/*`)
- Middleware: `['auth', 'role:admin']`
- `GET /admin` -> `AdminDashboardController`
- `GET /admin/konsultasi`, `GET /admin/konsultasi/{id}` -> `AdminConsultationController`
- `POST /admin/konsultasi/{id}/balasan` -> `AdminConsultationController@storeReply`
- `POST /admin/konsultasi/{id}/penawaran` -> `AdminConsultationController@storeOffer`
- `POST /admin/konsultasi/{id}/penawaran/{offer}/kirim` -> `AdminConsultationController@sendOffer`
- `GET /admin/proyek`, `POST /admin/proyek`, `PATCH /admin/proyek/{id}/status` -> `AdminProjectController`
- `POST /admin/termin/{term}/pembayaran` -> `AdminProjectController@recordPayment`
- `GET /admin/kalender`, `POST /admin/kalender/penanda` -> `AdminCalendarController`
- `GET /admin/konten`, `POST /admin/konten`, `PATCH /admin/konten/{id}`, `DELETE /admin/konten/{id}` -> `AdminContentController`
- `GET /admin/laporan`, `GET /admin/laporan/{report}/{format}` -> `AdminReportController`
- `GET /admin/pengguna`, `POST /admin/pengguna/admin`, `PATCH /admin/pengguna/{id}/peran`, `PATCH /admin/pengguna/{id}/aktif` -> `AdminUserController`

---

## 4. Pemetaan Spesifikasi Perubahan (CHG-01 s/d CHG-12)

| ID | Deskripsi | Status di Repo | File Target / Terdampak |
|---|---|---|---|
| **CHG-01** | Alur Cerita Dulu (AI memahami sebelum rekomendasi) | **SEBAGIAN** | `resources/views/welcome.blade.php`, `app/Services/ChatbotService.php` / `app/Services/ItineraryService.php`, rute API/Web rekomendasi cerita |
| **CHG-02** | Gerbang Login & Tindakan Tertunda | **SEBAGIAN** | `app/Http/Controllers/Public/ConsultationController.php`, `AuthenticatedSessionController.php`, session `pending_action` |
| **CHG-03** | Proposal Lengkap (Perluasan Skema Penawaran) | **SEBAGIAN** | Migration baru, `app/Models/Penawaran.php`, `PenawaranHari.php`, `PenawaranFoto.php`, `app/Services/PenawaranService.php` |
| **CHG-04** | Unduh PDF Proposal | **BELUM** | `barryvdh/laravel-dompdf`, `resources/views/pdf/proposal.blade.php`, `AdminConsultationController`, `Account\ConsultationController` |
| **CHG-05** | Foto, Logo & Pratinjau Langsung Editor Proposal | **BELUM** | `resources/views/admin/consultations/show.blade.php`, Alpine.js preview, storage symlink |
| **CHG-06** | Negosiasi via Minta Revisi & Duplikasi Versi Proposal | **SEBAGIAN** | `PenawaranService::buatVersiBaru` sudah ada; perlu menyalin field proposal baru (`pemahaman`, cakupan, `penawaran_hari`, foto) dan form revisi penawaran di klien |
| **CHG-07** | Tujuan Bebas (dalam/luar negeri) | **BELUM** | Agent/Service rekomendasi, validasi schema `sumber: 'database' \| 'bebas'` |
| **CHG-08** | Segmen Klien & 3 Layanan Baru | **SEBAGIAN** | Migration kolom `tipe_klien` & `hasil_diinginkan`, `WebsiteContentSeeder.php`, `resources/views/pages/consultations/create.blade.php` |
| **CHG-09** | Bagian Keselamatan & Operasional di Proposal | **BELUM** | Bagian dari CHG-03 (kolom `keselamatan` di `penawaran` dan template proposal PDF) |
| **CHG-10** | Bagian Mengapa Empirise & Tombol WhatsApp | **SEBAGIAN** | `config/empirise.php` (tambah nomor WA), `resources/views/welcome.blade.php` |
| **CHG-11** | Modul Admin Sisa (Kapasitas, Pengguna, Audit, Laporan) | **SUDAH ADA** | Terverifikasi lengkap dan tes berjalan 100% |
| **CHG-12** | Opsi Penawaran Berdampingan (Hemat/Standar/Premium) | **BELUM** | Tahap 7 (P2, opsional) |

---

## 5. Kesimpulan & Rekomendasi Langkah Kerja
Fondasi kode sangat bersih dan terstruktur. Tidak ada utang teknis Filament/Livewire. Seluruh perubahan CHG-01 sampai CHG-11 dapat dieksekusi bertahap sesuai urutan Tahap 1 hingga Tahap 6 pada `docs/CHANGE_SPEC.md`.
