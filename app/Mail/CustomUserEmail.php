<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class CustomUserEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $subject;
    public $emailBody;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $subject, $emailBody)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->emailBody = $emailBody;
        
        // Set queue for better performance
        $this->onQueue('emails');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
            from: config('mail.from.address', 'noreply@docratech.com'),
            replyTo: config('mail.from.address', 'noreply@docratech.com'),
            tags: ['custom-email', 'admin-communication'],
            metadata: [
                'user_id' => $this->user->id,
                'sent_by' => auth()->id() ?? 'system',
                'timestamp' => now()->toDateTimeString(),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.custom-user-email',
            with: [
                'user' => $this->user,
                'subject' => $this->subject,
                'emailBody' => $this->emailBody,
                'sentBy' => auth()->user() ?? null,
                'sentAt' => now(),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
} 