<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AdminAuditLogger
{
    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    public function record(
        User $actor,
        string $action,
        string $object,
        ?int $objectId = null,
        ?array $before = null,
        ?array $after = null,
    ): void {
        AuditLog::query()->create([
            'id_user' => $actor->getKey(),
            'aksi' => $action,
            'objek' => $object,
            'id_objek' => $objectId,
            'sebelum' => $before,
            'sesudah' => $after,
        ]);
    }
}
