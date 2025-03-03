<?php

namespace App\Mail;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $guest;
    public $invitation;

    /**
     * Create a new message instance.
     */
    public $subject;
    public $template;

    /**
     * Create a new message instance.
     */
    public function __construct(Guest $guest, Invitation $invitation, $subject = null, $template = null)
    {
        $this->guest = $guest;
        $this->invitation = $invitation;
        $this->subject = $subject;
        $this->template = $template;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject ?? 'UNDANGAN',
        );
    }

    /**
    /**
     * Get the message content definition.
      * Get the message content definition.
      */
     public function content(): Content
     {
         return new Content(
             view: 'emails.invitation',
             with: [
                 'invitation' => $this->invitation,
                 'customTemplate' => $this->template
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
