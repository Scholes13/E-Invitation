<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RsvpController extends Controller
{
    // Admin RSVP Dashboard - Shows all RSVP statuses
    public function index()
    {
        // Check if RSVP is enabled
        $setting = Setting::first();
        if (!$setting->enable_rsvp) {
            return redirect()->back()->with('error', 'RSVP feature is not enabled. Please enable it in settings first.');
        }

        $invitations = Invitation::select([
            'id_invitation',
            'name_guest',
            'email_guest',
            'phone_guest',
            'rsvp_status',
            'plus_ones_count',
            'plus_ones_names',
            'dietary_preferences',
            'rsvp_notes',
            'rsvp_responded_at'
        ])->orderBy('name_guest')->get();

        $stats = [
            'total' => $invitations->count(),
            'pending' => $invitations->where('rsvp_status', 'pending')->count(),
            'yes' => $invitations->where('rsvp_status', 'yes')->count(),
            'no' => $invitations->where('rsvp_status', 'no')->count(),
            'maybe' => $invitations->where('rsvp_status', 'maybe')->count(),
            'total_attending' => $invitations->where('rsvp_status', 'yes')->sum('plus_ones_count') + $invitations->where('rsvp_status', 'yes')->count(),
        ];

        return view('rsvp.index', compact('invitations', 'stats', 'setting'));
    }

    // Admin RSVP Settings
    public function settings()
    {
        $setting = Setting::first();
        return view('rsvp.settings', compact('setting'));
    }

    // Update RSVP Settings
    public function updateSettings(Request $request)
    {
        $request->validate([
            'enable_rsvp' => 'boolean',
            'rsvp_deadline' => 'nullable|date',
            'enable_plus_ones' => 'boolean',
            'collect_dietary_preferences' => 'boolean',
            'send_rsvp_reminders' => 'boolean',
            'reminder_days_before_deadline' => 'required|integer|min:1|max:30',
            'rsvp_email_template' => 'nullable|string',
            'rsvp_email_subject' => 'nullable|string|max:255',
        ]);

        $setting = Setting::first();
        $setting->update($request->all());

        return redirect()->back()->with('success', 'RSVP settings updated successfully.');
    }

    // Guest-facing RSVP form
    public function guestRsvpForm($qrcode)
    {
        $setting = Setting::first();
        
        // Check if RSVP is enabled
        if (!$setting->enable_rsvp) {
            return redirect()->back()->with('error', 'RSVP is not enabled for this event.');
        }

        // Check if deadline has passed
        if ($setting->rsvp_deadline && Carbon::parse($setting->rsvp_deadline)->isPast()) {
            return view('rsvp.deadline-passed');
        }

        $invitation = Invitation::where('qrcode_invitation', $qrcode)->first();
        
        if (!$invitation) {
            return view('rsvp.not-found');
        }

        return view('rsvp.form', compact('invitation', 'setting'));
    }

    // Process RSVP submission
    public function processRsvp(Request $request, $qrcode)
    {
        $setting = Setting::first();
        
        // Check if RSVP is enabled
        if (!$setting->enable_rsvp) {
            return redirect()->back()->with('error', 'RSVP is not enabled for this event.');
        }

        // Validate request
        $validationRules = [
            'rsvp_status' => 'required|in:yes,no,maybe',
            // Removed notes validation as the field has been removed from the form
            // 'rsvp_notes' => 'nullable|string|max:500',
        ];

        // Conditional validation based on settings
        if ($setting->enable_plus_ones && $request->rsvp_status == 'yes') {
            $validationRules['plus_ones_count'] = 'required|integer|min:0|max:5';
            $validationRules['plus_ones_names'] = 'nullable|string|max:255';
        }

        if ($setting->collect_dietary_preferences && $request->rsvp_status == 'yes') {
            $validationRules['dietary_preferences'] = 'nullable|string|max:500';
        }

        $request->validate($validationRules);

        // Get the invitation
        $invitation = Invitation::where('qrcode_invitation', $qrcode)->first();
        
        if (!$invitation) {
            return redirect()->back()->with('error', 'Invitation not found.');
        }

        // Update invitation with RSVP data
        $invitation->rsvp_status = $request->rsvp_status;
        // No longer updating notes since the field was removed
        // $invitation->rsvp_notes = $request->rsvp_notes;
        $invitation->rsvp_responded_at = now();

        if ($request->rsvp_status == 'yes') {
            if ($setting->enable_plus_ones) {
                $invitation->plus_ones_count = $request->plus_ones_count ?? 0;
                $invitation->plus_ones_names = $request->plus_ones_names;
            }

            if ($setting->collect_dietary_preferences) {
                $invitation->dietary_preferences = $request->dietary_preferences;
            }
        } else {
            // Reset plus ones and dietary preferences if not attending
            $invitation->plus_ones_count = 0;
            $invitation->plus_ones_names = null;
            $invitation->dietary_preferences = null;
        }

        $invitation->save();

        return redirect()->route('rsvp.thank-you', ['qrcode' => $qrcode]);
    }

    // Thank you page after RSVP submission
    public function thankYou($qrcode)
    {
        $invitation = Invitation::where('qrcode_invitation', $qrcode)->first();
        
        if (!$invitation) {
            return view('rsvp.not-found');
        }

        return view('rsvp.thank-you', compact('invitation'));
    }

    // Send RSVP reminders to guests who haven't responded
    public function sendReminders()
    {
        $setting = Setting::first();
        
        // Check if RSVP and reminders are enabled
        if (!$setting->enable_rsvp || !$setting->send_rsvp_reminders) {
            return redirect()->back()->with('error', 'RSVP reminders are not enabled.');
        }

        // Check if deadline is set and not in the past
        if (!$setting->rsvp_deadline || Carbon::parse($setting->rsvp_deadline)->isPast()) {
            return redirect()->back()->with('error', 'RSVP deadline has passed or is not set.');
        }

        // Find invitations that haven't responded and haven't been reminded recently
        $invitations = Invitation::where('rsvp_status', 'pending')
            ->where(function($query) {
                $query->whereNull('rsvp_reminder_sent_at')
                    ->orWhere('rsvp_reminder_sent_at', '<=', now()->subDays(7));
            })
            ->get();

        $count = 0;
        foreach ($invitations as $invitation) {
            // Send reminder email logic here
            // For demo purposes, we'll just update the reminder_sent_at timestamp
            $invitation->rsvp_reminder_sent_at = now();
            $invitation->save();
            $count++;

            // In a real implementation, you would send an email:
            // Mail::to($invitation->email_guest)->send(new RsvpReminderMail($invitation));
        }

        return redirect()->back()->with('success', "Sent reminders to {$count} guests.");
    }
}
