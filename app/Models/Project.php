<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nomor_proyek', 'id_penawaran', 'id_konsultasi', 'id_klien', 'nama', 'tgl_mulai', 'tgl_selesai', 'status', 'total_nilai', 'catatan'])]
class Project extends Model
{
    protected $table = 'proyek';

    protected $primaryKey = 'id_proyek';

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
            'total_nilai' => 'integer',
        ];
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'id_konsultasi', 'id_konsultasi');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_klien');
    }

    public function terms(): HasMany
    {
        return $this->hasMany(PaymentTerm::class, 'id_proyek', 'id_proyek')->orderBy('urutan');
    }
}
