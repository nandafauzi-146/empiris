<?php

return [
    'whatsapp_number' => env('EMPIRISE_WHATSAPP_NUMBER', '6281234567890'),

    'rate_limits' => [
        'login' => [
            'attempts' => 5,
            'period_minutes' => 1,
        ],
        'password_reset' => [
            'attempts' => 3,
            'period_minutes' => 1,
        ],
        'register' => [
            'attempts' => 5,
            'period_minutes' => 1,
        ],
        'email_verification' => [
            'attempts' => 6,
            'period_minutes' => 1,
        ],
        'consultation' => [
            'attempts' => 5,
            'period_minutes' => 60,
        ],
        'consultation_replies' => [
            'attempts' => 20,
            'period_minutes' => 1,
        ],
        'chatbot' => [
            'attempts' => 30,
            'period_minutes' => 60,
        ],
    ],

    'proposal' => [
        'berlaku_hari' => 14,
        'cakupan_termasuk' => 'Transportasi sesuai itinerary; Konsumsi sesuai program; Fasilitator dan pendamping tim Empirise; Tiket dan kunjungan sesuai itinerary; Dokumentasi foto kegiatan; Asuransi perjalanan.',
        'cakupan_tidak' => 'Pengeluaran pribadi peserta; Biaya di luar program; Perubahan jadwal dari pihak peserta.',
        'syarat' => 'Harga berlaku selama masa berlaku proposal; Perubahan jumlah peserta dapat memengaruhi harga; Ketentuan pembatalan disepakati saat deal; Ketersediaan final dikonfirmasi tim Empirise.',
        'keselamatan' => 'Tim tersertifikasi P3K dan didukung SOP tanggap darurat resmi; Asuransi perjalanan aktif untuk seluruh rombongan; Pengecekan armada dan rute sebelum keberangkatan; Pusat bantuan darurat 24 jam selama kegiatan berlangsung.',
        'pengesahan' => 'Persetujuan resmi dilakukan klien melalui tombol Setuju di akun Empirise. Dokumen ini bukan faktur.',
        'biaya_default' => [
            ['nama_item' => 'Transportasi', 'satuan' => 'paket', 'tipe_jumlah' => 'fixed', 'jumlah_bawaan' => 1, 'harga_default' => 0],
            ['nama_item' => 'Akomodasi (bila lebih dari 1 hari)', 'satuan' => 'orang-malam', 'tipe_jumlah' => 'peserta_x_hari_minus_1', 'jumlah_bawaan' => 0, 'harga_default' => 0],
            ['nama_item' => 'Konsumsi', 'satuan' => 'orang-hari', 'tipe_jumlah' => 'peserta_x_hari', 'jumlah_bawaan' => 1, 'harga_default' => 0],
            ['nama_item' => 'Fasilitator dan kegiatan', 'satuan' => 'paket', 'tipe_jumlah' => 'fixed', 'jumlah_bawaan' => 1, 'harga_default' => 0],
            ['nama_item' => 'Tiket dan kunjungan', 'satuan' => 'orang', 'tipe_jumlah' => 'peserta', 'jumlah_bawaan' => 1, 'harga_default' => 0],
            ['nama_item' => 'Dokumentasi', 'satuan' => 'paket', 'tipe_jumlah' => 'fixed', 'jumlah_bawaan' => 1, 'harga_default' => 0],
            ['nama_item' => 'Asuransi perjalanan', 'satuan' => 'orang', 'tipe_jumlah' => 'peserta', 'jumlah_bawaan' => 1, 'harga_default' => 0],
            ['nama_item' => 'Koordinasi tim lapangan', 'satuan' => 'paket', 'tipe_jumlah' => 'fixed', 'jumlah_bawaan' => 1, 'harga_default' => 0],
        ],
    ],
];
