<?php

namespace App\Http\Controllers;

use App\Mail\InvitationMail;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BlastingController extends Controller
{
    public function index()
    {
        $invitations = Invitation::with('guest')->get();
        return view('blasting.index', compact('invitations'));
    }

    public function send(Request $request)
    {
        try {
            if ($request->has('id_invitation')) {
                // Send to specific invitation
                $invitation = Invitation::with('guest')->findOrFail($request->id_invitation);
                $this->sendInvitation($invitation);
                return redirect()->back()->with('success', 'Invitation email sent successfully.');
            } else if ($request->send_type === 'unsent') {
                // Send to all unsent invitations
                $invitations = Invitation::with('guest')
                    ->whereHas('guest', function($query) {
                        $query->whereNotNull('email_guest');
                    })
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
                        Log::error("Failed to send invitation to {$invitation->guest->email_guest}: " . $e->getMessage());
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

    private function sendInvitation(Invitation $invitation)
    {
        if (!$invitation->guest->email_guest) {
            throw new \Exception("Guest {$invitation->guest->name_guest} has no email address.");
        }

        try {
            $setting = \App\Models\Setting::first();
            
            // Process email subject template
            $subject = $setting->email_subject_template ?? 'Wedding Invitation for {{ $guest->name_guest }}';
            // Remove @ symbols from template variables before rendering
            $subject = str_replace('@{{', '{{', $subject);
            $subject = Blade::render($subject, [
                'guest' => $invitation->guest,
                'invitation' => $invitation
            ]);

            // Process email body template
            $template = $setting->email_template_blasting;
            if ($template) {
                // Remove @ symbols from template variables before rendering
                $template = str_replace('@{{', '{{', $template);
                $template = Blade::render($template, [
                    'guest' => $invitation->guest,
                    'invitation' => $invitation
                ]);
            }

            Mail::to($invitation->guest->email_guest)
                ->send(new InvitationMail($invitation->guest, $invitation, $subject, $template));

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
}
