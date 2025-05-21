<?php

namespace App\Listeners;

use jdavidbakr\MailTracker\Events\EmailSentEvent;
use App\Models\Invitation;
use Illuminate\Support\Facades\Log;

class EmailSent
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmailSentEvent $event): void
    {
        try {
            // Get the tracked email
            $sentEmail = $event->sent_email;
            
            // Get the header that we added with the invitation code
            // The getHeader method is part of the SentEmail model
            $invitationCode = $sentEmail->getHeader('X-Invitation-Code');
            
            if ($invitationCode) {
                // Find the invitation and update status
                $invitation = Invitation::where('qrcode_invitation', $invitationCode)->first();
                if ($invitation) {
                    Log::info("Email sent to {$invitation->email_guest} with code {$invitationCode}", [
                        'time' => now(),
                        'message_id' => $sentEmail->message_id
                    ]);
                    
                    // Update invitation tracking data
                    $invitation->update([
                        'email_sent' => true,
                        'email_read' => false,
                        'email_bounced' => false
                    ]);
                } else {
                    Log::warning("Invitation not found for code {$invitationCode} in mail-tracker sent event");
                }
            } else {
                Log::warning("No invitation code found in email headers for mail-tracker sent event");
            }
        } catch (\Exception $e) {
            Log::error("Error processing mail-tracker sent event: {$e->getMessage()}", [
                'exception' => $e
            ]);
        }
    }
} 