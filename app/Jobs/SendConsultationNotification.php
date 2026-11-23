<?php

namespace App\Jobs;

use App\Mail\ConsultationReceived;
use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class SendConsultationNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(public int $emailLogId) {}

    public function handle(): void
    {
        $emailLog = EmailLog::query()
            ->with('consultation')
            ->findOrFail($this->emailLogId);

        if ($emailLog->status === 'terkirim') {
            return;
        }

        Mail::to($emailLog->ke_email)->send(
            new ConsultationReceived(
                $emailLog->consultation,
                $emailLog->jenis === 'notifikasi_admin',
            ),
        );

        $emailLog->update([
            'status' => 'terkirim',
            'terkirim_at' => now(),
            'error' => null,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        if ($exception === null) {
            return;
        }

        EmailLog::query()
            ->whereKey($this->emailLogId)
            ->update([
                'status' => 'gagal',
                'error' => Str::limit($exception->getMessage(), 1000),
            ]);
    }
}
