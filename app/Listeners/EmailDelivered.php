<?php

namespace App\Listeners;

use jdavidbakr\MailTracker\Events\EmailDeliveredEvent;
use App\Models\Invitation;
use Illuminate\Support\Facades\Log;

class EmailDelivered
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
    public function handle(EmailDeliveredEvent $event): void
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
                    Log::info("Email delivered to {$invitation->email_guest} with code {$invitationCode}", [
                        'email_address' => $event->email_address,
                        'time' => now()
                    ]);
                    
                    // Update invitation tracking data
                    $invitation->update([
                        'email_sent' => true,
                        'email_bounced' => false,
                        'email_delivered' => true,
                        'email_delivery_time' => now()
                    ]);
                } else {
                    Log::warning("Invitation not found for code {$invitationCode} in mail-tracker delivery event");
                }
            } else {
                Log::warning("No invitation code found in email headers for mail-tracker delivery event");
            }
        } catch (\Exception $e) {
            Log::error("Error processing mail-tracker delivery event: {$e->getMessage()}", [
                'exception' => $e
            ]);
        }
    }
} 