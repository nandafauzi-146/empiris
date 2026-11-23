<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['preferensi_json', 'hasil_json', 'latensi_ms'])]
class RecommendationLog extends Model
{
    protected $table = 'rekomendasi_log';

    protected $primaryKey = 'id_log';

    protected function casts(): array
    {
        return [
            'preferensi_json' => 'array',
            'hasil_json' => 'array',
            'latensi_ms' => 'integer',
        ];
    }
}
