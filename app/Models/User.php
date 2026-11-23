<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'phone', 'role', 'password', 'aktif'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'id_klien');
    }

    public function claimGuestConsultations(): void
    {
        if ($this->role !== UserRole::Klien || ! $this->hasVerifiedEmail()) {
            return;
        }

        Consultation::query()
            ->whereNull('id_klien')
            ->whereRaw('LOWER(email) = ?', [Str::lower($this->email)])
            ->update(['id_klien' => $this->getKey()]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'aktif' => 'boolean',
        ];
    }
}
