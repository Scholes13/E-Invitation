<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class BlastingController extends Controller
{
    public function index()
    {
        $invitations = Invitation::all();
        return view('blasting.index', compact('invitations'));
    }

    public function send(Request $request)
    {
        try {
            if ($request->has('id_invitation')) {
                // Send to specific invitation
                $invitation = Invitation::findOrFail($request->id_invitation);
                $this->sendInvitation($invitation);
                return redirect()->back()->with('success', 'Invitation email sent successfully.');
            } else if ($request->send_type === 'unsent') {
                // Send to all unsent invitations
                $invitations = Invitation::whereNotNull('email_guest')
                    ->where('email_sent', false)
                    ->get();

                $count = 0;
                foreach ($invitations as $invitation) {
                    try {
                        if ($invitation instanceof Invitation) {
                            $this->sendInvitation($invitation);
                            $count++;
                        }
                    } catch (\Exception $e) {
                        // Log the error but continue with other invitations
                        Log::error("Failed to send invitation to {$invitation->email_guest}: " . $e->getMessage());
                    }
                }

                return redirect()->back()->with('success', "{$count} invitation emails sent successfully.");
            }

            return redirect()->back()->with('error', 'Invalid request.');
        } catch (\Exception $e) {
            Log::error('Email blasting error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send invitation emails. Please try again.');
        }
    }

    /**
     * Track email opens
     */
    public function track($code)
    {
        try {
            // Find invitation by the tracking code
            $invitation = Invitation::where('qrcode_invitation', $code)->first();
            
            if ($invitation) {
                // Update to mark email as read
                $invitation->update([
                    'email_read' => true
                ]);
            }
            
            // Return a 1x1 transparent pixel
            $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
            return Response::make($pixel, 200, [
                'Content-Type' => 'image/gif',
                'Content-Length' => strlen($pixel),
            ]);
        } catch (\Exception $e) {
            Log::error('Email tracking error: ' . $e->getMessage());
            // Still return pixel even if there's an error to avoid breaking email client
            $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
            return Response::make($pixel, 200, [
                'Content-Type' => 'image/gif',
                'Content-Length' => strlen($pixel),
            ]);
        }
    }

    private function sendInvitation(Invitation $invitation)
    {
        if (!$invitation->email_guest) {
            throw new \Exception("Guest {$invitation->name_guest} has no email address.");
        }

        try {
            $setting = \App\Models\Setting::first();
            
            // Process email subject template with new variable format
            $subject = $setting->email_subject_template ?? 'Invitation for {name}';
            $subject = $this->replaceTemplateVariables($subject, $invitation);

            // Process email body template with new variable format
            $template = $setting->email_template_blasting;
            if ($template) {
                $template = $this->replaceTemplateVariables($template, $invitation);
                
                // Add tracking pixel to email
                $trackingUrl = url("/track-email/{$invitation->qrcode_invitation}");
                $trackingPixel = '<img src="'.$trackingUrl.'" width="1" height="1" alt="" style="display:none">';
                $template .= $trackingPixel;
            }

            Mail::to($invitation->email_guest)
                ->send(new InvitationMail($invitation, $subject, $template));

            // Update invitation status
            $invitation->update([
                'email_sent' => true,
                'email_read' => false,
                'email_bounced' => false
            ]);

        } catch (\Exception $e) {
            // Update invitation status for bounced email
            $invitation->update([
                'email_sent' => true,
                'email_bounced' => true
            ]);
            throw $e;
        }
    }
    
    /**
     * Replace template variables with actual values
     */
    private function replaceTemplateVariables($content, Invitation $invitation)
    {
        $replacements = [
            '{name}' => $invitation->name_guest,
            '{qrcode}' => $invitation->qrcode_invitation,
            '{company}' => $invitation->company_guest ?? '',
            '{table}' => $invitation->table_number_invitation ?? '',
            '{type}' => $invitation->type_invitation ?? '',
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
