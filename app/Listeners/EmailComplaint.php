<?php

namespace App\Listeners;

use jdavidbakr\MailTracker\Events\ComplaintMessageEvent;
use App\Models\Invitation;
use Illuminate\Support\Facades\Log;

class EmailComplaint
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
    public function handle(ComplaintMessageEvent $event): void
    {
        try {
            // Get the tracked email
            $sentEmail = $event->sent_email;
            
            // Get the header that we added with the invitation code
            $invitationCode = $sentEmail->getHeader('X-Invitation-Code');
            
            if ($invitationCode) {
                // Find the invitation and update status
                $invitation = Invitation::where('qrcode_invitation', $invitationCode)->first();
                if ($invitation) {
                    Log::warning("Email complaint received for {$invitation->email_guest} with code {$invitationCode}", [
                        'email_address' => $event->email_address,
                        'time' => now()
                    ]);
                    
                    // Update invitation tracking data to mark as complaint
                    $invitation->update([
                        'email_complaint' => true,
                        'email_complaint_time' => now(),
                        'email_status' => 'complained'
                    ]);
                } else {
                    Log::warning("Invitation not found for code {$invitationCode} in mail-tracker complaint event");
                }
            } else {
                Log::warning("No invitation code found in email headers for mail-tracker complaint event");
            }
        } catch (\Exception $e) {
            Log::error("Error processing mail-tracker complaint event: {$e->getMessage()}", [
                'exception' => $e
            ]);
        }
    }
} 