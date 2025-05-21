<?php

namespace App\Listeners;

use jdavidbakr\MailTracker\Events\LinkClickedEvent;
use App\Models\Invitation;
use Illuminate\Support\Facades\Log;

class EmailLinkClicked
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
    public function handle(LinkClickedEvent $event): void
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
                    Log::info("Email link clicked by {$invitation->name_guest} with code {$invitationCode}", [
                        'ip' => $event->ip_address, 
                        'link' => $event->link_url,
                        'time' => now()
                    ]);
                    
                    // Update invitation tracking data
                    $invitation->update([
                        'email_read' => true,
                        'last_tracked_at' => now(),
                        'tracking_method' => 'link_click',
                        'tracking_count' => $invitation->tracking_count + 1 ?? 1,
                    ]);
                } else {
                    Log::warning("Invitation not found for code {$invitationCode} from mail-tracker link click");
                }
            } else {
                Log::warning("No invitation code found in email headers for mail-tracker link click");
            }
        } catch (\Exception $e) {
            Log::error("Error processing mail-tracker link click event: {$e->getMessage()}", [
                'exception' => $e
            ]);
        }
    }
} 