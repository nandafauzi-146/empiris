<?php

namespace App\Jobs;

use App\Mail\ConsultationReplyReceived;
use App\Models\ConsultationReply;
use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class SendConsultationReplyNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public function __construct(
        public int $emailLogId,
        public int $replyId,
    ) {}

    public function handle(): void
    {
        $emailLog = EmailLog::query()->findOrFail($this->emailLogId);

        if ($emailLog->status === 'terkirim') {
            return;
        }

        $reply = ConsultationReply::query()
            ->with('consultation')
            ->findOrFail($this->replyId);

        Mail::to($emailLog->ke_email)->send(new ConsultationReplyReceived($reply));

        $reply->update(['terkirim_email' => true]);
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
