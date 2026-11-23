<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_konsultasi', 'id_pengirim', 'pesan', 'terkirim_email'])]
class ConsultationReply extends Model
{
    protected $table = 'balasan_konsultasi';

    protected $primaryKey = 'id_balasan';

    protected function casts(): array
    {
        return [
            'terkirim_email' => 'boolean',
        ];
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'id_konsultasi', 'id_konsultasi');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengirim');
    }
}
