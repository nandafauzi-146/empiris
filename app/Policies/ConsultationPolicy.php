<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Consultation;
use App\Models\User;

class ConsultationPolicy
{
    public function batal(User $user, Consultation $consultation): bool
    {
        return $user->role === UserRole::Klien
            && $consultation->id_klien === $user->getKey()
            && $consultation->status->canBeCancelled();
    }
}
