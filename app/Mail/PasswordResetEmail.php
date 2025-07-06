<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class PasswordResetEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $resetUrl;
    
    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->resetUrl = config('app.url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address', 'noreply@docratech.com'),
            subject: 'Reset Your DocraTech Medical Platform Password',
            tags: ['password-reset', 'security'],
            metadata: [
                'user_id' => $this->user->id,
                'reset_token' => substr($this->token, 0, 8) . '...', // Partial token for tracking
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.password-reset',
            with: [
                'user' => $this->user,
                'token' => $this->token,
                'resetUrl' => $this->resetUrl,
                'userName' => $this->user->first_name . ' ' . $this->user->last_name,
                'supportEmail' => config('mail.support.address', 'support@docratech.com'),
                'companyName' => config('app.name', 'DocraTech Medical Platform'),
                'expiryMinutes' => config('auth.passwords.users.expire', 60),
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