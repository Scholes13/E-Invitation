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
     * Send invitation email using mail-tracker for tracking
     */
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
            
            // Log email attempt
            Log::info("Attempting to send email to {$invitation->email_guest}", [
                'name' => $invitation->name_guest,
                'qrcode' => $invitation->qrcode_invitation,
            ]);
            
            if ($template) {
                $template = $this->replaceTemplateVariables($template, $invitation);
            }

            // Debug log before sending
            Log::info("About to send email to {$invitation->email_guest} using mail-tracker");
            
            try {
                // Create the mailable instance
                $mailable = new InvitationMail($invitation, $subject, $template);
                
                // Send email with mail-tracker
                // First try with log mailer for debugging
                Log::info("Sending to log mailer first for debugging");
                Mail::mailer('log')->to($invitation->email_guest)->send($mailable);
                
                // Now send the actual email
                Log::info("Now sending actual email");
                Mail::to($invitation->email_guest)->send($mailable);
                
                Log::info("Email sent successfully to {$invitation->email_guest}");
                
                // Update invitation status
                $invitation->update([
                    'email_sent' => true,
                    'email_bounced' => false
                ]);
                
            } catch (\Exception $e) {
                Log::error("Failed to send email: " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'email' => $invitation->email_guest ?? 'unknown'
                ]);
                
                // Update invitation status for bounced email
                $invitation->update([
                    'email_sent' => true,
                    'email_bounced' => true
                ]);
                
                throw $e;
            }

        } catch (\Exception $e) {
            // Update invitation status for bounced email
            $invitation->update([
                'email_sent' => true,
                'email_bounced' => true
            ]);
            
            Log::error("Failed to send email: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'email' => $invitation->email_guest ?? 'unknown'
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
