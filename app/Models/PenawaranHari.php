<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_penawaran',
    'urutan',
    'judul',
    'isi',
])]
class PenawaranHari extends Model
{
    protected $table = 'penawaran_hari';

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
