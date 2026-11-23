<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['mode', 'id_klien', 'ringkasan_pertanyaan', 'terjawab', 'status', 'latensi_ms'])]
class ChatbotLog extends Model
{
    protected $table = 'chatbot_log';

    protected $primaryKey = 'id_log';

    protected function casts(): array
    {
        return ['terjawab' => 'boolean', 'latensi_ms' => 'integer'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_klien');
    }
}
