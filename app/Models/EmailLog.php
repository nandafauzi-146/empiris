<?php

namespace App\Models;

use Database\Factories\EmailLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_konsultasi',
    'jenis',
    'ke_email',
    'subjek',
    'status',
    'jadwal_kirim',
    'terkirim_at',
    'error',
])]
class EmailLog extends Model
{
    /** @use HasFactory<EmailLogFactory> */
    use HasFactory;

    protected $table = 'email_log';

    protected $primaryKey = 'id_email';

    protected function casts(): array
    {
        return [
            'jadwal_kirim' => 'datetime',
            'terkirim_at' => 'datetime',
        ];
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'id_konsultasi', 'id_konsultasi');
    }
}
