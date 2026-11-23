<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'id_penawaran',
    'urutan',
    'nama_item',
    'deskripsi',
    'jumlah',
    'satuan',
    'harga_satuan',
    'asal',
])]
class DetailPenawaran extends Model
{
    protected $table = 'detail_penawaran';

    protected $primaryKey = 'id_detail';

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'harga_satuan' => 'integer',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Penawaran::class, 'id_penawaran', 'id_penawaran');
    }
}
