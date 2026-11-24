# PERTANYAAN TERBUKA (OPEN QUESTIONS)
**Website Empirise Jalan-Jalan**  
**Dokumen Rujukan:** `docs/CHANGE_SPEC.md` Lampiran B, `AGENTS.md`

---

Catatan: Setiap pertanyaan di bawah mencantumkan asumsi teraman yang digunakan sistem saat ini agar pengembangan fitur tidak terblokir.

| # | Topik | Pertanyaan ke Pemilik / Owner | Asumsi Teraman yang Diterapkan di Kode | Status |
|---|---|---|---|---|
| **OQ-01** | Nomor WhatsApp Resmi | Berapa nomor WhatsApp resmi Empirise Jalan-Jalan dan di mana saja tombolnya ingin ditampilkan? | Menggunakan konfigurasi `config('empirise.whatsapp_number', '6281234567890')`. Tombol WhatsApp click-to-chat `https://wa.me/{nomor}` ditampilkan di navigasi/header, footer, dan kartu kontak. Nomor dapat diubah kapan saja di `.env` (`EMPIRISE_WHATSAPP_NUMBER`). | **Asumsi Berjalan** |
| **OQ-02** | Legalitas & Dokumen Usaha | Dokumen legalitas apa saja yang boleh dicantumkan (NIB, SIUP, sertifikasi kepariwisataan), dan portofolio/klien mana yang memiliki persetujuan resmi publikasi? | Menampilkan data legalitas terverifikasi pada dokumen proposal PDF formal (NIB/Izin Usaha berstatus template resmi di konfigurasi). Portofolio publik memakai data seeder yang sudah disetujui di beranda dan halaman tentang. | **Asumsi Berjalan** |
| **OQ-03** | Standar Biaya & Termin | Berapa acuan nilai `harga_default` per satuan dan apakah skema pembayaran selalu termin bertahap 30/40/30 atau variatif? | Menggunakan konfigurasi daftar harga default di `config/empirise.php`. Nilai biaya proposal diisi otomatis sebagai rancangan awal saat admin membuat proposal, dan admin dapat mengedit nominal bebas sebelum mengirim ke klien. | **Asumsi Berjalan** |
| **OQ-04** | Validasi Tujuan Bebas | Apakah destinasi bebas (yang tidak ada di katalog database) boleh langsung disetujui otomatis masuk draf proposal, atau wajib melalui peninjauan kurasi admin? | Sesuai aturan CHG-07: AI menyarankan destinasi bebas dengan label `sumber = bebas` tanpa klaim fasilitas/jam buka. Admin tetap menjadi pemutus tunggal (R4/R5) saat menyusun dan mengirim proposal final. | **Asumsi Berjalan** |
| **OQ-05** | Opsi Penawaran Berdampingan (CHG-12) | Apakah format penawaran klien memerlukan 3 paket berdampingan (Hemat, Standar, Premium) atau cukup 1 paket proposal utama dengan opsi negosiasi revisi? | Diletakkan pada prioritas P2 (Tahap 7). Format proposal utama berfokus pada 1 versi proposal komprehensif dengan fitur negosiasi dan riwayat versi (CHG-03 & CHG-06). | **Menunggu Keputusan Owner (P2)** |
| **OQ-06** | Masa Berlaku Proposal & Pembatalan | Berapa hari masa berlaku bawaan penawaran/proposal dan bagaimana klausul pembatalan standar? | Masa berlaku bawaan proposal disetel 14 hari kalender (`berlaku_hari = 14`). Klausul pembatalan menggunakan teks standar Lampiran A pada `config/empirise.php`, dan admin dapat mengkustomisasi per proposal. | **Asumsi Berjalan** |
