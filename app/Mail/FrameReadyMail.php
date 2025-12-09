<?php

namespace App\Mail;

use App\Models\Frame;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FrameReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Frame $frame
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Increment email counter and refresh to get updated count
        $this->frame->increment('email_count');
        $this->frame->refresh();
        return new Envelope(
            from: config('fotoku.email.from_address'),
            // Include FOTOKU counter in subject for tracking
            subject: "FOTOKU #{$this->frame->email_count}: Foto Kenangan Indahmu Sudah Siap! 📸✨",
            replyTo: config('fotoku.email.reply_to'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Ensure expires_at has a value (7 days from now if null)
        $expiresAt = $this->frame->presigned_expires_at ?: now()->addDays(7);
        
        return new Content(
            view: 'emails.frame-ready',
            with: [
                'frame' => $this->frame,
                'photoSession' => $this->frame->photoSession->load('photos'),
                'customer_name' => $this->frame->photoSession->customer_name,
                'session_code' => $this->frame->photoSession->session_code,
                'download_url' => $this->frame->presigned_url ?: route('photobox.serve-frame', ['frame' => $this->frame->id]),
                'expires_at' => $expiresAt,
                // Pass FOTOKU counter to view
                'email_count' => $this->frame->email_count,
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
