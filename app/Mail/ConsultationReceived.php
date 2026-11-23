<?php

namespace App\Mail;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsultationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Consultation $consultation,
        public bool $forAdmin = false,
    ) {}

    public function envelope(): Envelope
    {
        $adminEmail = config('services.empirise.admin_email');

        return new Envelope(
            from: new Address(config('mail.from.address'), 'Admin Empirise'),
            replyTo: [new Address($adminEmail, 'Admin Empirise')],
            subject: $this->forAdmin
                ? 'Konsultasi baru dari '.$this->consultation->nama
                : 'Konsultasi Anda sudah kami terima',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.consultation-received',
            with: [
                'consultation' => $this->consultation,
                'forAdmin' => $this->forAdmin,
                'homeUrl' => route('home'),
            ],
        );
    }
}
