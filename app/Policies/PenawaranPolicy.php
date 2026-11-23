<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PenawaranStatus;
use App\Models\Penawaran;
use App\Models\User;

class PenawaranPolicy
{
    public function tanggapi(User $user, Penawaran $offer): bool
    {
        return $user->role->value === 'klien'
            && $offer->consultation->id_klien === $user->getKey()
            && $offer->status === PenawaranStatus::Dikirim;
    }
}
