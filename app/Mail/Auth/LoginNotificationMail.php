<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected readonly User $user,
        protected readonly string $loggedInAt
    ) {}


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Login Detected',
        );
    }
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.login-notification',
            with: [
                'firstName' => $this->user->first_name,
                'lastName' => $this->user->last_name,
                'email' => $this->user->email,
                'loggedInAt' => $this->loggedInAt,
            ],
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
