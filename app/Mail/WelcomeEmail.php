<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WelcomeEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $loginUrl;
    
    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $password = null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->loginUrl = config('app.url') . '/login';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address', 'noreply@docratech.com'),
            subject: 'Welcome to DocraTech Medical Platform - Your Account is Ready',
            tags: ['welcome', 'user-onboarding'],
            metadata: [
                'user_id' => $this->user->id,
                'user_role' => $this->user->role?->name ?? 'user',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.welcome',
            with: [
                'user' => $this->user,
                'password' => $this->password,
                'loginUrl' => $this->loginUrl,
                'userName' => $this->user->first_name . ' ' . $this->user->last_name,
                'roleName' => $this->user->role?->display_name ?? $this->user->role?->name ?? 'User',
                'supportEmail' => config('mail.support.address', 'support@docratech.com'),
                'companyName' => config('app.name', 'DocraTech Medical Platform'),
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