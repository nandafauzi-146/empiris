<?php

namespace App\Mail;

use App\Models\ConsultationReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ConsultationReplyReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ConsultationReply $reply) {}

    public function envelope(): Envelope
    {
        $adminEmail = config('services.empirise.admin_email');

        return new Envelope(
            from: new Address(config('mail.from.address'), 'Admin Empirise'),
            replyTo: [new Address($adminEmail, 'Admin Empirise')],
            subject: 'Balasan baru untuk konsultasi Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.consultation-reply-received',
            text: 'emails.text.consultation-reply-received',
            with: [
                'reply' => $this->reply,
                'replyExcerpt' => Str::limit($this->reply->pesan, 400),
                'replyIsTruncated' => Str::length($this->reply->pesan) > 400,
                'consultationUrl' => route('account.consultations.show', $this->reply->consultation),
            ],
        );
    }
}
