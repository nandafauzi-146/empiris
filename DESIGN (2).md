# DESIGN.md
## Sistem Desain Website Empirise Jalan-Jalan

| | |
|---|---|
| **Versi** | 1.0 (Draft) |
| **Tanggal** | 12 November 2026 |
| **Cakupan** | Situs publik (landing page), area klien, panel admin |
| **Sumber** | Screenshot landing page (1489 px) dan 7 tangkapan layar admin (pratinjau Figma) |
| **Dokumen terkait** | `AGENTS.md`, `ADMIN_UI_SPEC.md`, `SDD.md`, `PANDUAN_DESAIN.md` |

Berkas ini adalah **sumber kebenaran visual**. AI agent wajib membacanya sebelum menulis Blade, CSS, atau komponen UI. Jangan menulis warna, ukuran, atau radius langsung di komponen. Pakai token di Bagian 3 dan 4.

---

## 0. Tingkat Kepercayaan Nilai

| Penanda | Arti |
|---|---|
| **[diukur]** | Diambil dari piksel tangkapan layar (warna admin). Akurat |
| **[perkiraan]** | Diperkirakan dari tampilan (ukuran, jarak, radius). Cocokkan dengan Figma saat implementasi |
| **[usulan]** | Tidak ada di desain. Diusulkan agar sistem lengkap. Perlu persetujuan |

Ukuran piksel CSS untuk admin diperkirakan dengan asumsi tangkapan layar berskala 2x pada viewport sekitar 1680 px.

---

## 1. Karakter Visual

**Kata kunci:** bersih, hangat, tepercaya, ringan. Banyak ruang putih, satu warna aksen merah, sudut membulat besar, bayangan lembut, huruf Poppins.

| Prinsip | Penerapan |
|---|---|
| **Satu aksen** | Merah brand dipakai hemat: tombol utama, status aktif, eyebrow, indikator penting. Sisanya netral |
| **Kartu dan kapsul** | Konten dalam kartu putih bersudut besar. Kontrol berbentuk pill (kapsul penuh) |
| **Lembut, bukan datar** | Bayangan tipis berlapis, bukan garis tebal |
| **Konsisten publik dan admin** | Font, merah, radius, dan ikon sama. Admin lebih padat dan netral |
| **Status lewat warna dan teks** | Warna pastel dengan teks gelap berkontras. Tidak pernah warna saja |
| **Foto sebagai bintang di publik** | Foto besar dengan overlay gelap. Teks putih di atasnya |

---

## 2. Dasar

| Hal | Nilai |
|---|---|
| Font | **Poppins** (Google Fonts), bobot 300, 400, 500, 600, 700. Cadangan: `ui-sans-serif, system-ui, sans-serif` |
| Bahasa | Indonesia |
| Ikon | **Lucide**, garis 1.5 px, ukuran 16, 20, 24 px. Paket `blade-lucide-icons` |
| Mata uang | Rupiah. `Rp 35.400.000` |
| Zona waktu | WIB |
| Tailwind | Nilai di bawah adalah **heksadesimal eksplisit**. Daftarkan sebagai token sendiri, jangan bergantung pada palet bawaan Tailwind (hasil sampel admin identik dengan palet Tailwind v4, tetapi v3 berbeda sedikit) |

---

## 3. Token

### 3.1 Warna Brand

| Token | Hex | Pemakaian | Sumber |
|---|---|---|---|
| `brand-500` | `#D32F2F` | Tombol utama, FAB, tanggal terpilih, segmen aktif, ikon menu aktif, eyebrow, teks tautan penting | [diukur] |
| `brand-50` | `#FDE8E8` | Latar kotak ikon, tombol `soft`, tombol Masuk | [perkiraan] |
| `brand-700` | `#B71C1C` | Hover dan pressed tombol merah, teks merah di atas `brand-50` | [usulan] |
| `brand-logo` | `#FF0000` | Hanya untuk logo | [perkiraan] |

Teks merah di atas `brand-50` memakai `brand-700` agar kontras 5,6:1. `brand-500` di atas `brand-50` hanya 4,2:1 (lolos untuk teks besar dan tebal saja).

### 3.2 Netral

| Token | Hex | Pemakaian | Sumber |
|---|---|---|---|
| `ink` | `#171717` | Judul, teks utama admin, pill aktif | [diukur] |
| `ink-publik` | `#1A1A1A` | Judul situs publik | [perkiraan] |
| `dark` | `#262626` | Tombol gelap (`Keluar`, `Excel`), avatar, tepi fokus kartu | [diukur] |
| `black` | `#0A0A0A` | Latar CTA dan footer situs publik | [perkiraan] |
| `neutral-600` | `#525252` | Teks segmen tidak aktif | [diukur] |
| `neutral-500` | `#737373` | Teks sekunder, label, judul kolom | [diukur] |
| `body-publik` | `#6B6B6B` | Paragraf situs publik | [perkiraan] |
| `neutral-400` | `#A1A1A1` | **Dekoratif saja** (kontras 2,6:1). Jangan untuk teks yang harus dibaca | [diukur] |
| `neutral-100` | `#F5F5F5` | Lintasan segmen, latar halaman bagian atas | [diukur] |
| `neutral-50` | `#FAFAFA` | Baris di dalam kartu | [diukur] |
| `surface` | `#FFFFFF` | Kartu, baris daftar | [diukur] |
| `border` | `#EDEDED` | Garis kartu dan pemisah publik | [perkiraan] |

### 3.3 Permukaan

| Token | Hex | Pemakaian | Sumber |
|---|---|---|---|
| `page-bg` | gradasi `#EDEDED` ke `#F5F5F5` | Latar halaman admin | [diukur] |
| `sidebar-bg` | `#FBFAFB` | Kartu sidebar | [diukur] |
| `panel-bg` | `#F9F9F9` | Kartu konten utama admin | [diukur] |
| `row-bg` | `#FFFFFF` dan `#FAFAFA` | Baris dan kartu di dalam panel | [diukur] |
| `surface-warm` | `#FFFAFA` | Latar section portofolio publik | [perkiraan] |

### 3.4 Warna Status (Pasangan Latar dan Teks)

Semua dari piksel desain [diukur]. Kontras teks terhadap latar tercantum.

| Makna | Dipakai untuk | Latar | Teks | Kontras |
|---|---|---|---|---|
| Info | Konsultasi **Baru**, proyek **Terjadwal** | `#EFF6FE` | `#0F48E2` | 6,3:1 |
| Peringatan | Konsultasi **Dibalas**, proyek **Berjalan** | `#FFFBEB` | `#BB4D00` | 4,9:1 |
| Proses | Konsultasi **Penawaran** | `#F5F3FE` | `#700CE3` | 6,7:1 |
| Sukses | Konsultasi **Deal**, termin **Lunas** | `#ECFDF5` | `#007A57` | 5,1:1 |
| Bahaya | Termin **Belum bayar** | `#FEF2F2` | `#C10006` | 5,9:1 |
| Mendesak | **Perlu ditanggapi** | `#D32F2F` | `#FFFFFF` | 5,0:1 |
| Tersedia | Tanggal kalender tersedia (badge) | `#ECFDF5` | `#006046` | 7,2:1 |
| Netral | **Batal**, **Selesai** (bila bukan aktif) | `#F5F5F5` | `#525252` | 7,2:1 |

**Titik kalender (grafis, selalu disertai teks status):** Tersedia `#00BC7D` [perkiraan], Terbatas `#FFB900` [diukur], Penuh `#FB2C36` [diukur].

**Bintang rating:** `#FBBF24` [perkiraan].

### 3.5 Tipografi

Poppins. Ukuran dalam px CSS.

**Situs publik** [perkiraan]

| Peran | Ukuran / tinggi baris | Bobot | Catatan |
|---|---|---|---|
| Hero (`SOLO`) | 72 / 1.0 | 700 | Huruf kapital, putih |
| Judul section (H2) | 32 / 1.25 | 600 | Tengah, maks 680 px |
| Judul CTA | 24 / 1.3 | 600 | Putih di latar hitam |
| Eyebrow | 11 / 1.4 | 600 | Kapital, `letter-spacing: 0.2em`, merah |
| Judul kartu | 15 / 1.4 | 600 | |
| Paragraf | 14 / 1.6 | 300 sampai 400 | `body-publik` |
| Teks kecil | 12 / 1.5 | 400 | |
| Tombol, menu | 13 sampai 14 / 1 | 500 | |

**Admin** [perkiraan]

| Peran | Ukuran / tinggi baris | Bobot | Warna |
|---|---|---|---|
| Judul halaman | 28 / 1.2 | 600 | `ink` |
| Angka kartu statistik | 48 / 1 | 300 | `ink` |
| Judul kartu bagian | 18 / 1.3 | 600 | `ink` |
| Judul baris (nama, proyek) | 15 / 1.4 | 500 sampai 600 | `ink` |
| Teks baris, menu | 14 sampai 15 / 1.5 | 400 | `ink` |
| Label dan keterangan | 13 / 1.4 | 400 | `neutral-500` |
| Kode dan tanggal (`K-1001 / 6 Nov 2026`) | 12 / 1.4 | 400 | `neutral-500` |
| Badge | 12 / 1 | 500 | Lihat 3.4 |
| Judul kolom tabel | 11 / 1.3 | 500 | `neutral-500`, kapital, `letter-spacing: 0.06em` |

Angka uang dan jumlah memakai `font-variant-numeric: tabular-nums` agar sejajar [usulan].

### 3.6 Spasi

Basis **4 px**. Skala: 4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 80, 96.

| Pemakaian | Nilai |
|---|---|
| Padding kartu admin | 24 sampai 32 |
| Jarak antar kartu | 16 sampai 24 |
| Jarak antar section publik | 80 sampai 96 |
| Padding tombol | 10 vertikal, 20 sampai 24 horizontal |
| Tinggi baris daftar | 56 sampai 72 |

### 3.7 Radius

| Token | Nilai | Pemakaian |
|---|---|---|
| `r-sm` | 8 px | Chip kecil, input |
| `r-md` | 12 px | Kotak ikon |
| `r-lg` | 16 px | Kartu layanan, klien, testimoni, baris daftar admin |
| `r-xl` | 24 px | Kartu slider publik, kartu statistik, kartu bagian |
| `r-2xl` | 28 sampai 32 px | Sidebar, panel konten admin |
| `r-full` | 9999 px | Tombol, pill, badge, FAB, avatar |

### 3.8 Bayangan

| Token | Nilai | Pemakaian |
|---|---|---|
| `shadow-card` | `0 1px 2px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.05)` | Kartu putih |
| `shadow-nav-active` | `0 2px 8px rgba(0,0,0,.08)` | Menu aktif admin |
| `shadow-fab` | `0 10px 24px rgba(211,47,47,.35)` | Tombol chat melayang, tombol merah menonjol |
| `shadow-feature` | `0 20px 40px rgba(211,47,47,.25)` | Kartu portofolio aktif |

Semua [perkiraan].

### 3.9 Tata Letak

| Hal | Nilai |
|---|---|
| Kontainer situs publik | maks **1112 px**, tengah |
| Desain referensi publik | 1489 px |
| Kontainer admin | maks **±1280 px**, tengah [perkiraan] |
| Lebar sidebar | **±280 px** [perkiraan] |
| Jarak sidebar ke konten | 16 px [perkiraan] |
| Padding panel konten | 32 px [perkiraan] |
| Breakpoint | `sm 640`, `md 768`, `lg 1024`, `xl 1280` |

### 3.10 Gerak

| Token | Nilai | Pemakaian |
|---|---|---|
| `dur-fast` | 150 ms | Hover, fokus |
| `dur-base` | 200 ms | Menu, badge, segmen |
| `dur-slow` | 400 ms | Cross-fade hero, perpindahan slide |
| `ease` | `cubic-bezier(.2,.8,.2,1)` | Semua transisi |

Wajib menghormati `prefers-reduced-motion`: matikan autoplay dan ganti transisi jadi seketika.

---

## 4. Implementasi Token

Sumber kebenaran: **variabel CSS** di `resources/css/app.css`. Tailwind membacanya.

```css
:root {
  --brand-500: #D32F2F;  --brand-50: #FDE8E8;  --brand-700: #B71C1C;
  --ink: #171717;        --dark: #262626;      --black: #0A0A0A;
  --n-600: #525252;      --n-500: #737373;     --n-400: #A1A1A1;
  --n-100: #F5F5F5;      --n-50: #FAFAFA;      --surface: #FFFFFF;
  --border: #EDEDED;
  --sidebar-bg: #FBFAFB; --panel-bg: #F9F9F9;
  --info-bg: #EFF6FE;    --info-fg: #0F48E2;
  --warn-bg: #FFFBEB;    --warn-fg: #BB4D00;
  --proc-bg: #F5F3FE;    --proc-fg: #700CE3;
  --ok-bg: #ECFDF5;      --ok-fg: #007A57;
  --bad-bg: #FEF2F2;     --bad-fg: #C10006;
  --dot-ok: #00BC7D;     --dot-limit: #FFB900; --dot-full: #FB2C36;
  --r-lg: 16px; --r-xl: 24px; --r-2xl: 32px;
  --shadow-card: 0 1px 2px rgba(0,0,0,.05), 0 6px 20px rgba(0,0,0,.05);
}
```

**Tailwind 4** (`@theme` di `app.css`):

```css
@theme {
  --font-sans: "Poppins", ui-sans-serif, system-ui, sans-serif;
  --color-brand-500: var(--brand-500);
  --color-brand-50: var(--brand-50);
  --color-brand-700: var(--brand-700);
  --color-ink: var(--ink);
  --radius-lg: var(--r-lg);
  --radius-xl: var(--r-xl);
  --radius-2xl: var(--r-2xl);
}
```

**Tailwind 3** (`tailwind.config.js`, `theme.extend`): petakan nama yang sama ke `colors`, `borderRadius`, `boxShadow`, dan `fontFamily.sans`.

Aturan: komponen memakai kelas token (`bg-brand-500`, `text-ink`, `rounded-xl`), bukan hex.

---

## 5. Ikonografi

Lucide, garis, satu gaya. Warna ikon mewarisi teks, kecuali menu aktif (merah) dan ikon hapus (merah).

| Konteks | Ikon (kebab-case, `x-lucide-*`) |
|---|---|
| Layanan | `plane`, `party-popper`, `mountain`, `bus`, `users` |
| Navigasi admin | `layout-dashboard`, `message-square`, `folder-kanban` atau `wallet`, `calendar`, `file-pen`, `bar-chart-3`, `users`, `bot` |
| Aksi | `plus`, `pencil`, `trash-2`, `upload`, `download`, `arrow-right`, `chevron-down`, `chevron-left`, `chevron-right`, `log-out`, `panel-left-close` |
| Lokasi dan info | `map-pin`, `calendar`, `clock` |
| Status | `check`, `x`, `alert-circle` |
| Chat | `bot`, `send`, `message-circle` |

Ukuran: 16 px di dalam badge dan baris, 20 px di menu dan tombol, 24 px untuk ikon layanan. Ikon tombol ikon-saja wajib punya `aria-label`.

---

## 6. Komponen

### 6.1 Tombol

| Varian | Tampilan | Pemakaian |
|---|---|---|
| `primary` | Latar `brand-500`, teks putih, pill | Aksi utama: `Jelajahi`, `Mulai Konsultasi`, `+ Tambah`, `Setuju dan Lanjutkan` |
| `dark` | Latar `dark`, teks putih, pill | `Keluar`, `Excel` |
| `outline` | Latar putih, garis `border`, teks `ink`, pill | `CSV`, `Upload bukti`, `Semua portofolio` |
| `glass` | Latar putih transparan 10 persen, garis putih, teks putih | Di atas foto: `Lihat Semua` |
| `soft` | Latar `brand-50`, teks `brand-700` | `Masuk` di navbar |
| `icon` | Lingkaran 40 sampai 44 px | Panah slider, ciutkan sidebar |

| Ukuran | Tinggi | Teks |
|---|---|---|
| `sm` | 32 px | 12 px |
| `md` | 40 px | 13 sampai 14 px |
| `lg` | 48 px | 14 sampai 15 px |

Keadaan: hover `brand-700` (primary) atau naik satu tingkat kecerahan (lainnya); fokus cincin 2 px biru dengan jarak 2 px [diukur dari desain, kontras 6,7:1]; nonaktif opasitas 50 persen dan `cursor-not-allowed`; memuat: spinner kecil menggantikan ikon dan klik dikunci.

```blade
<x-button variant="primary" icon="arrow-right">Jelajahi</x-button>
```

### 6.2 Badge Status

Pill, tinggi 24 sampai 28 px, padding 10 sampai 12 px, teks 12 px bobot 500. Pasangan warna di 3.4.

```blade
<x-status-badge status="dibalas" />   {{-- peta status ke warna di satu tempat --}}
```

Peta status ke warna disimpan di satu tempat (enum atau komponen), bukan tersebar.

### 6.3 Kartu

| Varian | Spesifikasi |
|---|---|
| `card` | Putih, `r-lg`, `shadow-card`, padding 24 |
| `card-xl` (statistik, bagian) | Putih, `r-xl`, padding 24 sampai 32 |
| `card-list` (konsultasi, proyek) | Putih, `r-lg`, `shadow-card`, isi bertingkat: kode abu kecil, judul tebal, info |
| `card-row` | Latar `neutral-50`, `r-lg`, tinggi 56 sampai 64, panah di kanan, dipakai di dalam kartu bagian |

Kartu statistik: label `neutral-500` 14 px di atas, angka 48 px bobot 300 di bawah. Keadaan fokus atau terpilih: garis tepi 2 px `dark`. **Tidak boleh kosong**: selalu ada label dan angka (nol ditampilkan sebagai `0`).

### 6.4 Pill Filter (Tab)

Pill outline tinggi 40 px. Aktif: latar `ink`, teks putih. Dipakai di Konsultasi (status) dan Konten Web (jenis konten). Peran ARIA `tablist` dan `tab`, panah kiri kanan memindahkan fokus.

### 6.5 Segmented Control

Lintasan `neutral-100`, pill. Segmen aktif `brand-500` teks putih, tidak aktif teks `neutral-600`. Dipakai untuk status proyek (`Terjadwal`, `Berjalan`, `Selesai`). Peran `radiogroup`. Perubahan ke status akhir (`Selesai`) meminta konfirmasi.

### 6.6 Checkbox, Dropdown, Input

| Komponen | Spesifikasi |
|---|---|
| Checkbox | Kotak 20 px, radius 6, garis `neutral-400`, tercentang `brand-500`. Label 14 px di kanan |
| Dropdown peran | Pill tinggi 36 px, garis `border`, chevron merah. Nonaktif: teks dan chevron `neutral-500`, latar `neutral-50` |
| Input teks [usulan] | Tinggi 44 px, `r-sm` 8 sampai 12 px, garis `border`, latar putih. Fokus: cincin 2 px. Galat: garis `bad-fg` dan pesan di bawah |
| Textarea [usulan] | Sama dengan input, minimum 4 baris, penghitung karakter di kanan bawah bila ada batas |

### 6.7 Tabel

Dipakai di Laporan dan Pengguna. Judul kolom kapital 11 px `neutral-500`. Baris dipisah garis 1 px `border`, tinggi 48 sampai 56 px, tanpa latar zebra. Angka rata kanan dengan `tabular-nums`. Pada layar sempit, tabel menggulir horizontal di dalam kontainernya.

### 6.8 Sidebar Admin

| Bagian | Spesifikasi |
|---|---|
| Wadah | Kartu `sidebar-bg`, `r-2xl`, lebar ±280 px, tinggi penuh viewport dikurangi margin |
| Pengguna | Avatar 48 px `dark` dengan inisial putih, nama 15 px 600, email 13 px `neutral-500` |
| Label | `Panel Admin`, 13 px, `neutral-500` |
| Item menu | Tinggi 48 px, `r-full`, ikon 20 px dan teks 15 px. Aktif: latar putih, `shadow-nav-active`, ikon `brand-500`, teks 600 |
| Footer | Tombol `icon` persegi gelap (ciutkan) dan tombol `dark` penuh `Keluar` |
| Menciut | Hanya ikon, tooltip nama menu saat hover dan fokus |

### 6.9 Navbar Publik

Transparan di atas hero, teks putih. Logo di kiri. Menu tengah 13 sampai 14 px bobot 300 sampai 400, aktif `brand-500`. Kanan: `Daftar` (teks) dan `Masuk` (`soft`). Mobile: hamburger dan laci.

### 6.10 Judul Section Publik

Eyebrow (11 px, kapital, merah, spasi lebar), judul H2 32 px 600, subjudul `neutral-500` 14 px. Semua tengah. Komponen: `x-section-heading`.

### 6.11 Kartu Slider Hero (Glass)

Radius 24, bingkai putih tipis, foto penuh, gradasi gelap di bawah. Label bawah: ikon `map-pin`, wilayah kecil, nama kota tebal. Kartu aktif lebih besar (±235 x 390), lainnya ±175 x 280 dan semi transparan. Kontrol: dua tombol `icon` gelap transparan, garis progres dengan segmen merah, penghitung `02 / 12`.

### 6.12 Kartu Portofolio

Tengah: ±290 x 415, penuh warna, `shadow-feature`, chip kategori, kota, judul, tombol `Lihat dokumentasi` (primary sm). Samping: ±200 x 320, overlay abu gelap dan sebagian grayscale, makin jauh makin kecil. Chip kategori: pill putih transparan, teks putih 11 px.

### 6.13 Kalender Admin

| Bagian | Spesifikasi |
|---|---|
| Kepala | Bulan dan tahun 24 px 600, panah merah di kanan |
| Grid | 7 kolom `Sen` sampai `Min`, nama hari 12 px `neutral-500`, tanggal 15 px 600 |
| Tanggal terpilih | Lingkaran 48 px `brand-500`, teks putih, `shadow-fab` ringan |
| Titik | 6 px di bawah angka. Warna 3.4 |
| Hari di luar bulan | `neutral-400` (dekoratif, bukan informasi) |
| Legenda | Titik 8 px dan teks 13 px: Tersedia, Terbatas, Penuh |
| Panel kanan | Tanggal, badge status, bagian `Kegiatan`, bagian `PENANDA KAPASITAS` (kapital 11 px) berisi dua sakelar `Tim penuh` dan `Armada penuh` |

Status tidak boleh hanya berupa warna titik: panel dan `aria-label` tanggal menyebut statusnya ("14 November, Terbatas").

### 6.14 Kartu Proyek dan Termin

Kepala: kode abu kecil, judul tebal, nama klien, dan segmented control di kanan. Baris termin: nama, nominal (rata kanan, `tabular-nums`), badge, lalu bukti (`nama-berkas, tanggal`) atau tombol `Upload bukti`. Ringkasan di kanan bawah: `Sisa tagihan` dan nominal merah `#D32F2F` bila lebih dari 0, hijau `ok-fg` atau netral bila `Rp 0`.

### 6.15 Widget Chat [usulan]

Dirancang dari token yang ada karena belum ada di Figma.

| Bagian | Spesifikasi |
|---|---|
| FAB | Lingkaran 56 px `brand-500`, ikon `bot` putih 24 px, `shadow-fab`, kanan bawah 24 px |
| Panel | 380 x 560 px (mobile: layar penuh), `r-xl`, putih, `shadow-card` kuat |
| Header | Nama (`Asisten Empirise` atau `Asisten Akun`), tombol tutup. Mode akun menampilkan chip konteks |
| Pesan pengguna | Pill besar `brand-500`, teks putih, rata kanan |
| Pesan asisten | `neutral-50`, teks `ink`, rata kiri |
| Kartu destinasi | Foto 16:10, nama, wilayah, `r-lg` |
| Kartu itinerary | Judul, ringkasan, jumlah hari, tombol `Lihat Itinerary Lengkap` dan `Kirim ke Empirise` |
| Kartu draf (akun) | Isi dapat diedit, tombol `Kirim` (primary), `Ubah` (outline), `Batal` (teks) |
| Input | Kolom teks, penghitung karakter, tombol `send` |
| Catatan privasi | 12 px `neutral-500` di bawah input |

### 6.16 Timeline Itinerary [usulan]

Per hari: judul `Hari 1 - {tema}`. Garis vertikal 2 px `border` di kiri, titik 10 px `brand-500`, jam `neutral-500` 13 px, aktivitas 15 px 500, chip layanan pendukung (pill `neutral-100`). Bagian atas selalu ada label **Gambaran awal** (badge info) dan disclaimer tetap.

### 6.17 Dialog, Toast, Tooltip [usulan]

| Komponen | Spesifikasi |
|---|---|
| Dialog konfirmasi | Pusat layar, lebar 440 px, `r-xl`, judul 18 px 600, deskripsi `neutral-500`. Tombol: `outline` (Batal) dan `primary` atau `dark` (Konfirmasi). Aksi merusak memakai tombol merah dan menyebut objeknya |
| Toast | Kanan atas, pill atau kartu kecil, ikon dan teks, hilang 4 detik, dapat ditutup |
| Tooltip | Latar `dark`, teks putih 12 px, `r-sm`, muncul 300 ms setelah hover atau saat fokus (terlihat pada menu admin) |

### 6.18 Keadaan Kosong, Memuat, Galat [usulan]

| Keadaan | Tampilan |
|---|---|
| Kosong | Ikon 32 px `neutral-400`, kalimat singkat `neutral-500` (`Belum ada kegiatan pada tanggal ini.`), tombol aksi bila relevan |
| Memuat | Kerangka abu `neutral-100` berdenyut halus pada bentuk kartu atau baris |
| Galat | Ikon `alert-circle`, pesan jelas dalam bahasa Indonesia, tombol `Coba lagi` |

---

## 7. Pola Halaman

### 7.1 Landing Page (Publik)

Urutan: navbar, hero fullscreen dengan slider destinasi, Layanan (5 kartu), Portofolio (coverflow), Klien (grid logo 6 kolom), Testimoni (3 kartu), CTA dan footer hitam, FAB chat. Rincian ukuran per section ada di `PANDUAN_DESAIN.md` Bagian 5. Jarak vertikal 80 sampai 96 px.

### 7.2 Admin

```
page-bg (gradasi)
└─ kontainer maks 1280, tengah, jarak 16 px
   ├─ Sidebar (kartu melayang, 280 px)
   └─ Panel konten (kartu, flex-1)
      ├─ Judul halaman (28 px 600)
      ├─ Filter / aksi (pill, checkbox, tombol utama di kanan)
      └─ Isi (kartu daftar, kartu statistik, tabel)
```

Tombol utama halaman (`+ Tambah`) berada di kanan atas, sejajar judul. Filter di bawah judul. Satu tombol primary per layar.

### 7.3 Area Klien [usulan]

Belum ada di Figma. Pakai kerangka admin yang lebih ringan: sidebar atau tab atas (Konsultasi, Penawaran, Proyek, Pembayaran, Profil), kartu daftar dengan badge status yang sama. Halaman penawaran: tabel komponen, total, riwayat versi, tiga tombol `Setuju dan Lanjutkan` (primary), `Minta Revisi` (outline), `Tolak` (teks). Hindari menyamai admin sepenuhnya agar klien tidak merasa berada di panel internal.

### 7.4 Halaman Autentikasi [usulan]

Kartu putih tengah (`r-xl`, lebar 440), logo di atas, judul 24 px, input, tombol primary penuh, tautan `neutral-500`. Latar `page-bg`.

---

## 8. Konten dan Suara

| Hal | Aturan |
|---|---|
| Bahasa | Indonesia, sapaan **Anda**, nada hangat dan ringkas |
| Tombol | Kata kerja singkat, kapital di awal saja: `Jelajahi`, `Lihat Semua`, `Mulai Konsultasi`, `Lihat dokumentasi`, `Semua portofolio`, `Upload bukti`, `Kirim ke Empirise`, `Setuju dan Lanjutkan` |
| Judul section | Kalimat manfaat: `Satu mitra untuk seluruh kebutuhan kegiatan Anda` |
| Eyebrow | Kapital: `LAYANAN`, `PORTOFOLIO PILIHAN`, `KLIEN KAMI`, `TESTIMONI TERBARU` |
| Tanggal | `12 Nov 2026` di daftar, `12 November 2026` di dokumen dan email |
| Uang | `Rp 35.400.000`. Rentang: `Rp 50-75 juta` |
| Durasi | `3 hari 2 malam` |
| Kode | `K-1001` (konsultasi), `PR-201` (proyek) |
| Keadaan kosong | Kalimat lengkap dan tenang: `Belum ada kegiatan pada tanggal ini.` |
| Galat | Sebut masalah dan langkah: `Cerita maksimal 1500 karakter. Singkatkan lalu coba lagi.` |
| AI | Hangat, merangkum dulu. Selalu menyebut gambaran awal dan bukan penawaran |

---

## 9. Gambar dan Media

| Hal | Aturan |
|---|---|
| Foto hero | Rasio lebar, `object-cover`, overlay gradasi gelap (atas dan bawah) agar teks putih terbaca, minimal 1920 px |
| Foto kartu | 600 px, rasio 3:4 (slider) atau 16:10 (kartu destinasi chat) |
| Logo klien | Latar putih, `object-contain`, padding 12 px. **Dari berkas resmi, jangan digambar ulang** |
| Ikon | Lucide saja. Tanpa emoji di antarmuka |
| `alt` | Wajib dan bermakna. Dekoratif memakai `alt=""` |
| Pemuatan | `loading="lazy"`, `width` dan `height` ditetapkan agar tidak bergeser |

---

## 10. Aksesibilitas

Rasio kontras dihitung dari warna sebenarnya (WCAG 2.1, ambang 4,5:1 teks normal, 3:1 teks besar dan grafis).

| Pasangan | Rasio | Keputusan |
|---|---|---|
| Putih di atas `brand-500` | 4,98 | Lolos |
| `brand-500` di atas putih | 4,98 | Lolos |
| `brand-500` di atas `brand-50` | 4,24 | **Hanya teks besar atau tebal**. Pakai `brand-700` (5,60) untuk teks biasa |
| `ink` di atas putih | 17,9 | Lolos |
| `neutral-500` di atas putih | 4,74 | Lolos |
| `neutral-500` di atas `panel-bg` | 4,50 | Lolos (batas). Jangan lebih terang |
| `neutral-400` di atas putih | **2,58** | **Gagal untuk teks**. Email di kartu pengguna sidebar memakainya, ganti ke `neutral-500` |
| `body-publik` `#6B6B6B` di atas putih | 5,33 | Lolos |
| Putih di atas `dark` | 15,1 | Lolos |
| Semua badge status (3.4) | 4,9 sampai 7,2 | Lolos |
| Titik kuning `#FFB900` di atas putih | 1,72 | **Gagal sebagai satu-satunya penanda**. Selalu sertakan teks |
| Titik hijau `#00BC7D` di atas putih | 2,47 | Gagal sebagai satu-satunya penanda. Selalu sertakan teks |
| Titik merah `#FB2C36` di atas putih | 3,81 | Lolos untuk grafis, tetap sertakan teks |

Aturan tambahan:

- Fokus selalu terlihat: cincin 2 px biru (terlihat di desain, kontras 6,7:1 di atas putih), jarak 2 px. Jangan `outline: none` tanpa pengganti.
- Area sentuh minimal **44 x 44 px** (tombol ikon bulat 40 px diberi padding tak terlihat).
- Urutan tab mengikuti urutan visual. Dialog menangkap fokus dan mengembalikannya saat ditutup.
- Pill filter memakai peran `tablist`. Segmented control memakai `radiogroup`.
- Daftar pesan chat `aria-live="polite"`.
- Hormati `prefers-reduced-motion` dan `prefers-contrast`.
- Tooltip bukan satu-satunya pemberi nama: tombol ikon wajib `aria-label`.

---

## 11. Responsif

| Lebar | Publik | Admin |
|---|---|---|
| 1280 ke atas | Sesuai desain | Sidebar penuh |
| 1024 sampai 1279 | Kontainer menyempit, slider hero 2 kartu kecil | Sidebar penuh, konten lebih sempit |
| 768 sampai 1023 | Layanan 3 kolom, klien 4 kolom, testimoni 2 kolom, slider hero di bawah teks | Sidebar menciut jadi ikon |
| di bawah 768 | Hamburger, H1 hero 48 px, layanan 1 kolom atau gulir horizontal, klien 3 kolom, testimoni 1 kolom, portofolio 1 kartu aktif | Sidebar menjadi laci, kartu statistik 2 kolom, tabel menggulir di kontainer |

Tidak boleh ada gulir horizontal pada halaman. Pengecualian: tabel dan carousel di dalam kontainernya sendiri.

---

## 12. Boleh dan Tidak Boleh

**Boleh**

- Memakai token dan komponen yang ada. Menambah varian bila benar-benar baru, lalu mendaftarkannya di berkas ini.
- Satu aksi utama merah per layar.
- Badge dengan teks dan warna.
- Konfirmasi untuk aksi yang merusak atau tak terbalik.
- Keadaan kosong, memuat, dan galat untuk setiap daftar.

**Tidak boleh**

- Menulis hex, ukuran, atau radius langsung di komponen.
- Memakai `neutral-400` untuk teks yang harus dibaca.
- Memakai warna sebagai satu-satunya pembeda status.
- Menggunakan merah untuk dekorasi biasa (merah berarti aksi atau perhatian).
- Mencampur gaya ikon (isi dan garis) atau memakai emoji di antarmuka.
- Membiarkan kartu statistik kosong.
- Menampilkan harga di keluaran AI publik (lihat `AGENTS.md` R2).
- Menggambar ulang logo klien atau memakai foto tanpa izin.
- Membuat tombol utama lebih dari satu per area.
- Menonaktifkan indikator fokus.

---

## 13. Daftar Periksa untuk Agent

Sebelum menyatakan tugas UI selesai:

- [ ] Semua warna, radius, dan bayangan berasal dari token (tidak ada hex di Blade).
- [ ] Font Poppins termuat dan bobot sesuai Bagian 3.5.
- [ ] Komponen memakai varian di Bagian 6, bukan gaya sendiri.
- [ ] Badge status lewat satu peta komponen.
- [ ] Kontras sesuai Bagian 10. Tidak ada teks `neutral-400`.
- [ ] Fokus terlihat, tombol ikon punya `aria-label`, area sentuh 44 px.
- [ ] Ada keadaan kosong, memuat, dan galat.
- [ ] Responsif pada 1489, 1280, 1024, 768, dan 390 px tanpa gulir horizontal.
- [ ] Hormati `prefers-reduced-motion`.
- [ ] Dibandingkan dengan Figma atau tangkapan layar pada lebar desain.

---

## 14. Yang Belum Didesain di Figma

Bagian di berkas ini yang bertanda **[usulan]** tidak ada di Figma. Perlu didesain atau disetujui sebelum final:

| Area | Keterangan |
|---|---|
| Detail konsultasi admin dan editor penawaran | Inti alur deal. Belum ada |
| Halaman Chatbot AI admin | Belum ada tangkapan layar |
| Widget chat (publik dan akun) | Rancangan 6.15 hanya turunan dari token |
| Area klien | Dashboard, konsultasi, penawaran, proyek, pembayaran |
| Halaman autentikasi | Masuk, daftar, lupa password |
| Form tambah dan edit konten, dialog, toast | Admin |
| Halaman `/rencana` dan itinerary | Timeline 6.16 hanya turunan |
| Tampilan ponsel admin | Belum ada |
| Mode gelap | Di luar lingkup versi ini |

---

## 15. Catatan Temuan dari Desain

Hal yang sebaiknya diperbaiki di Figma atau diputuskan sebelum implementasi:

1. Kartu statistik pertama dashboard **kosong**.
2. Email di kartu pengguna sidebar berkontras 2,6:1 (gagal). Naikkan ke `neutral-500`.
3. `Rp 0` pada sisa tagihan masih merah. Gunakan hijau atau netral.
4. Cincin fokus tampil biru, tidak mengikuti merah brand. Disarankan dipertahankan karena kontrasnya baik.
5. Tombol chat merah melayang tampil di halaman admin. Tentukan fungsinya atau hilangkan.
6. Tombol segmen status proyek tidak memuat `Batal`. Sediakan aksi terpisah.
