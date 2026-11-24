<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_penawaran',
    'jenis',
    'nama_destinasi',
    'path',
    'keterangan',
    'urutan',
])]
class PenawaranFoto extends Model
{
    protected $table = 'penawaran_foto';

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function penawaran(): BelongsTo
    {
        return $this->belongsTo(Penawaran::class, 'id_penawaran', 'id_penawaran');
    }
}
