<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_proyek', 'urutan', 'nama', 'nominal', 'jatuh_tempo', 'status', 'jumlah_dibayar', 'tgl_bayar', 'bukti_path'])]
class PaymentTerm extends Model
{
    protected $table = 'termin_pembayaran';

    protected $primaryKey = 'id_termin';

    protected function casts(): array
    {
        return [
            'jatuh_tempo' => 'date',
            'tgl_bayar' => 'date',
            'nominal' => 'integer',
            'jumlah_dibayar' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'id_proyek', 'id_proyek');
    }
}
