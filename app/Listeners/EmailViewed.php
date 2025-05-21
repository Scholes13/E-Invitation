<?php

namespace App\Listeners;

use jdavidbakr\MailTracker\Events\ViewEmailEvent;
use App\Models\Invitation;
use Illuminate\Support\Facades\Log;

class EmailViewed
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
    public function handle(ViewEmailEvent $event): void
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
                    Log::info("Email viewed by {$invitation->name_guest} with code {$invitationCode}", [
                        'ip' => $event->ip_address, 
                        'time' => now()
                    ]);
                    
                    // Update invitation tracking data
                    $invitation->update([
                        'email_read' => true,
                        'last_tracked_at' => now()
                    ]);
                } else {
                    Log::warning("Invitation not found for code {$invitationCode} from mail-tracker");
                }
            } else {
                Log::warning("No invitation code found in email headers for mail-tracker event");
            }
        } catch (\Exception $e) {
            Log::error("Error processing mail-tracker view event: {$e->getMessage()}", [
                'exception' => $e
            ]);
        }
    }
} 