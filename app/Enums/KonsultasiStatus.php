<?php

declare(strict_types=1);

namespace App\Enums;

enum KonsultasiStatus: string
{
    case Baru = 'baru';
    case Dibalas = 'dibalas';
    case Penawaran = 'penawaran';
    case Deal = 'deal';
    case Batal = 'batal';

    public function label(): string
    {
        return match ($this) {
            self::Baru => 'Baru',
            self::Dibalas => 'Dibalas',
            self::Penawaran => 'Penawaran',
            self::Deal => 'Deal',
            self::Batal => 'Dibatalkan',
        };
    }

    public function canBeCancelled(): bool
    {
        return ! in_array($this, [self::Deal, self::Batal], true);
    }
}
