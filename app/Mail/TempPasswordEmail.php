<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TempPasswordEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $tempPassword;
    
    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $tempPassword)
    {
        $this->user = $user;
        $this->tempPassword = $tempPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address', 'noreply@docratech.com'),
            subject: 'Your DocraTech Medical Platform Password Has Been Reset',
            tags: ['temp-password', 'security', 'admin-action'],
            metadata: [
                'user_id' => $this->user->id,
                'reset_by' => 'admin',
                'timestamp' => now()->toISOString(),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.temp-password',
            with: [
                'user' => $this->user,
                'tempPassword' => $this->tempPassword,
                'userName' => $this->user->first_name . ' ' . $this->user->last_name,
                'supportEmail' => config('mail.support.address', 'support@docratech.com'),
                'companyName' => config('app.name', 'DocraTech Medical Platform'),
                'loginUrl' => config('app.url') . '/login',
                'profileUrl' => config('app.url') . '/profile',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 