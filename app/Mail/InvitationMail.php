<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The invitation instance.
     *
     * @var \App\Models\Invitation
     */
    public $invitation;
    
    /**
     * The email subject.
     *
     * @var string|null
     */
    public $emailSubject;
    
    /**
     * The custom email template.
     *
     * @var string|null
     */
    public $customTemplate;

    /**
     * Create a new message instance.
     */
    public function __construct(Invitation $invitation, ?string $subject = null, ?string $template = null)
    {
        $this->invitation = $invitation;
        $this->emailSubject = $subject;
        $this->customTemplate = $template;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject ?? 'UNDANGAN',
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Invitation-Code' => $this->invitation->qrcode_invitation,
                'X-Priority' => '1',
                'X-MSMail-Priority' => 'High',
                'Importance' => 'High',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->customTemplate ? 'emails.invitation' : 'emails.simple-invitation',
            with: [
                'invitation' => $this->invitation,
                'customTemplate' => $this->customTemplate,
                'companyName' => env('MAIL_FROM_NAME', 'Werkudara Group')
            ],
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
