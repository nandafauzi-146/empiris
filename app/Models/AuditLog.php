<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_user', 'aksi', 'objek', 'id_objek', 'sebelum', 'sesudah'])]
class AuditLog extends Model
{
    protected $table = 'audit_log';

    protected $primaryKey = 'id_audit';

    protected function casts(): array
    {
        return ['sebelum' => 'array', 'sesudah' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
