<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PenawaranStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'id_konsultasi',
    'versi',
    'status',
    'judul',
    'pemahaman',
    'catatan_admin',
    'cakupan_termasuk',
    'cakupan_tidak',
    'keselamatan',
    'syarat',
    'catatan_klien',
    'total',
    'berlaku_hari',
    'logo_path',
    'dikirim_at',
    'ditanggapi_at',
    'disetujui_at',
])]
class Penawaran extends Model
{
    protected $table = 'penawaran';

    protected $primaryKey = 'id_penawaran';

    protected function casts(): array
    {
        return [
            'status' => PenawaranStatus::class,
            'total' => 'integer',
            'berlaku_hari' => 'integer',
            'dikirim_at' => 'datetime',
            'ditanggapi_at' => 'datetime',
            'disetujui_at' => 'datetime',
        ];
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'id_konsultasi', 'id_konsultasi');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPenawaran::class, 'id_penawaran', 'id_penawaran')
            ->orderBy('urutan');
    }

    public function days(): HasMany
    {
        return $this->hasMany(PenawaranHari::class, 'id_penawaran', 'id_penawaran')
            ->orderBy('urutan');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PenawaranFoto::class, 'id_penawaran', 'id_penawaran')
            ->orderBy('urutan');
    }

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'id_penawaran', 'id_penawaran');
    }

    public function hasEmptyPrice(): bool
    {
        return $this->details()->whereNull('harga_satuan')->exists();
    }
}
