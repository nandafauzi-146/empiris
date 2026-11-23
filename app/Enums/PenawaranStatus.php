<?php

declare(strict_types=1);

namespace App\Enums;

enum PenawaranStatus: string
{
    case Draft = 'draft';
    case Dikirim = 'dikirim';
    case Disetujui = 'disetujui';
    case Revisi = 'revisi';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Dikirim => 'Menunggu tanggapan',
            self::Disetujui => 'Disetujui',
            self::Revisi => 'Perlu revisi',
            self::Ditolak => 'Ditolak',
        };
    }
}
