<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tanggal', 'jenis', 'dibuat_oleh'])]
class CapacityMarker extends Model
{
    protected $table = 'penanda_kapasitas';

    protected $primaryKey = 'id_penanda';

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
