<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $offer->judul ?: 'Proposal Penawaran Empirise' }}</title>
    <style>
        @page {
            margin: 28px 32px 32px 32px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1a1a1a;
            font-size: 11px;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #D32F2F;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #D32F2F;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .brand-tagline {
            font-size: 10px;
            color: #666;
            margin: 2px 0 0 0;
        }
        .header-meta {
            text-align: right;
            font-size: 9.5px;
            color: #555;
        }
        .doc-title-block {
            background-color: #FDE8E8;
            border-left: 4px solid #D32F2F;
            padding: 10px 14px;
            margin-bottom: 16px;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #991b1b;
            margin: 0 0 4px 0;
        }
        .section-heading {
            font-size: 11.5px;
            font-weight: bold;
            color: #111;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 4px;
            margin-top: 14px;
            margin-bottom: 8px;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 12px;
        }
        .info-grid td {
            vertical-align: top;
            padding: 2px 4px;
        }
        .info-label {
            width: 25%;
            color: #666;
            font-weight: 500;
        }
        .info-value {
            width: 75%;
            font-weight: bold;
            color: #111;
        }
        .cost-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 14px;
        }
        .cost-table th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: 600;
            text-align: left;
            padding: 7px 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .cost-table td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row td {
            background-color: #fef2f2;
            font-weight: bold;
            color: #991b1b;
            font-size: 11px;
            border-top: 2px solid #D32F2F;
        }
        .day-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 7px 10px;
            margin-bottom: 6px;
        }
        .day-title {
            font-weight: bold;
            color: #b91c1c;
            margin-bottom: 2px;
        }
        .bullet-list {
            margin: 4px 0 0 0;
            padding-left: 16px;
            color: #374151;
        }
        .bullet-list li {
            margin-bottom: 2px;
        }
        .two-col-table {
            width: 100%;
            margin-top: 6px;
        }
        .two-col-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 8px 0 0;
        }
        .footer-note {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #d1d5db;
            font-size: 8.5px;
            color: #6b7280;
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8.5px;
            font-weight: bold;
            border-radius: 3px;
            background-color: #e5e7eb;
            color: #374151;
        }
        .badge-green {
            background-color: #d1fae5;
            color: #065f46;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 55%; vertical-align: middle;">
                @if ($offer->logo_path && file_exists(public_path('storage/' . $offer->logo_path)))
                    <img src="{{ public_path('storage/' . $offer->logo_path) }}" style="max-height: 40px; margin-bottom: 4px;" alt="Logo Klien">
                @endif
                <h1 class="brand-title">EMPIRISE JALAN-JALAN</h1>
                <p class="brand-tagline">Biro Wisata &amp; Penyelenggara Kegiatan Kustom</p>
                <p style="margin: 3px 0 0 0; font-size: 9px; color: #555;">
                    Yogyakarta &bull; WhatsApp: {{ config('empirise.whatsapp_number', '6281234567890') }} &bull; Website: empirise.com
                </p>
            </td>
            <td style="width: 45%; vertical-align: middle;" class="header-meta">
                <p style="margin: 0; font-weight: bold; font-size: 11px; color: #111;">SURAT PENAWARAN RESMI</p>
                <p style="margin: 2px 0 0 0;">Nomor: <strong>{{ $offer->consultation->nomor_konsultasi }}/REV-{{ $offer->versi }}</strong></p>
                <p style="margin: 2px 0 0 0;">Tanggal Terbit: {{ $offer->dikirim_at ? $offer->dikirim_at->format('d F Y') : now()->format('d F Y') }}</p>
                <p style="margin: 2px 0 0 0; color: #b91c1c;">Masa Berlaku: {{ $offer->berlaku_hari }} Hari Kalender</p>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title-block">
        <h2 class="doc-title">{{ $offer->judul ?: 'Proposal Kegiatan ' . $offer->consultation->layanan }}</h2>
        <div style="font-size: 10px; color: #4b5563;">
            Disiapkan khusus untuk: <strong>{{ $offer->consultation->instansi ?: $offer->consultation->nama }}</strong>
            ({{ $offer->consultation->nama }})
        </div>
    </div>

    <!-- Ringkasan Kebutuhan -->
    <table class="info-grid" cellpadding="0" cellspacing="0">
        <tr>
            <td class="info-label">Layanan Utama</td>
            <td class="info-value">{{ $offer->consultation->layanan }}</td>
        </tr>
        <tr>
            <td class="info-label">Destinasi / Rute</td>
            <td class="info-value">{{ $offer->consultation->tujuan ?: 'Sesuai kesepakatan kegiatan' }}</td>
        </tr>
        <tr>
            <td class="info-label">Estimasi Peserta</td>
            <td class="info-value">{{ $offer->consultation->jumlah_peserta ? $offer->consultation->jumlah_peserta . ' Orang' : 'Sesuai konfirmasi rombongan' }}</td>
        </tr>
        <tr>
            <td class="info-label">Durasi &amp; Tanggal</td>
            <td class="info-value">
                {{ $offer->consultation->durasi_hari ? $offer->consultation->durasi_hari . ' Hari' : '' }}
                @if ($offer->consultation->tanggal_rencana)
                    (Rencana: {{ $offer->consultation->tanggal_rencana->format('d F Y') }})
                @endif
            </td>
        </tr>
    </table>

    <!-- Pemahaman Kebutuhan Klien -->
    @if ($offer->pemahaman)
        <div class="section-heading">Pemahaman Rencana Kegiatan</div>
        <div style="background-color: #fafafa; border: 1px solid #eee; padding: 7px 10px; border-radius: 4px; font-style: italic; color: #374151;">
            {{ $offer->pemahaman }}
        </div>
    @endif

    <!-- Rencana Hari Kegiatan (Itinerary) -->
    @if ($offer->days->isNotEmpty())
        <div class="section-heading">Rencana Hari Kegiatan (Itinerary)</div>
        @foreach ($offer->days as $day)
            <div class="day-box">
                <div class="day-title">{{ $day->judul }}</div>
                <div style="color: #4b5563;">{{ $day->isi }}</div>
            </div>
        @endforeach
    @endif

    <!-- Rincian Biaya / Anggaran -->
    <div class="section-heading">Rincian Anggaran &amp; Biaya Investasi Kegiatan</div>
    <table class="cost-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 45%;">Uraian Komponen Biaya</th>
                <th style="width: 15%;" class="text-center">Volume</th>
                <th style="width: 15%;" class="text-right">Harga Satuan (Rp)</th>
                <th style="width: 20%;" class="text-right">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($offer->details as $idx => $detail)
                @php
                    $subtotal = round((float) $detail->jumlah * (int) ($detail->harga_satuan ?? 0));
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $detail->nama_item }}</strong>
                        @if ($detail->deskripsi)
                            <br><span style="font-size: 8.5px; color: #6b7280;">{{ $detail->deskripsi }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format((float) $detail->jumlah, 0, ',', '.') }} {{ $detail->satuan }}</td>
                    <td class="text-right">
                        {{ $detail->harga_satuan !== null ? number_format((int) $detail->harga_satuan, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right">
                        {{ $detail->harga_satuan !== null ? number_format((int) $subtotal, 0, ',', '.') : '-' }}
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right" style="padding-right: 12px;">TOTAL ESTIMASI INVESTASI KEGIATAN</td>
                <td class="text-right">Rp {{ number_format($offer->total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Cakupan Fasilitas: Termasuk & Belum Termasuk -->
    <table class="two-col-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="section-heading">Fasilitas Sudah Termasuk (Inclusions)</div>
                <div style="font-size: 9.5px; color: #374151; line-height: 1.4;">
                    {{ $offer->cakupan_termasuk ?: config('empirise.proposal.cakupan_termasuk') }}
                </div>
            </td>
            <td>
                <div class="section-heading">Belum Termasuk (Exclusions)</div>
                <div style="font-size: 9.5px; color: #374151; line-height: 1.4;">
                    {{ $offer->cakupan_tidak ?: config('empirise.proposal.cakupan_tidak') }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Keselamatan & Operasional (CHG-09) -->
    <div class="section-heading">Standar Keselamatan &amp; Operasional Kegiatan (CHG-09)</div>
    <div style="font-size: 9.5px; color: #374151; line-height: 1.4; background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 6px 10px; border-radius: 4px;">
        {{ $offer->keselamatan ?: config('empirise.proposal.keselamatan') }}
    </div>

    <!-- Syarat & Ketentuan -->
    <div class="section-heading">Syarat &amp; Ketentuan Penawaran</div>
    <div style="font-size: 9px; color: #4b5563; line-height: 1.4;">
        {{ $offer->syarat ?: config('empirise.proposal.syarat') }}
    </div>

    <!-- Catatan Khusus Admin -->
    @if ($offer->catatan_admin)
        <div style="margin-top: 8px; font-size: 9px; color: #555;">
            <strong>Catatan Tambahan:</strong> {{ $offer->catatan_admin }}
        </div>
    @endif

    <!-- Pengesahan / Catatan Kaki Resmi (Lampiran A) -->
    <div class="footer-note">
        <strong>PENGESAHAN DOKUMEN:</strong>
        {{ config('empirise.proposal.pengesahan', 'Persetujuan resmi dilakukan klien melalui tombol Setuju di akun Empirise. Dokumen ini bukan faktur.') }}
        <br>
        Dokumen ini diterbitkan secara elektronik oleh Sistem Operasional Empirise Jalan-Jalan pada {{ now()->format('d/m/Y H:i') }} WIB.
    </div>

</body>
</html>
