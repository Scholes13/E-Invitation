<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function index()
    {
        $setting = DB::table('setting')->first();
        return view('setting.setting_app', compact('setting'));
    }

    public function update(Request $request)
    {
        // Call the existing settingAppUpdate method
        return $this->settingAppUpdate($request);
    }

    public function settingAppUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_app' => 'required|string|min:3|max:100',
            'logo_app' => 'nullable|image|max:512|mimes:jpg,jpeg,png',
            'image_bg_app' => 'nullable|image|max:1536|mimes:jpg,jpeg,png',
            'email_template_blasting' => 'nullable|string', // Add validator for email template
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $setting = DB::table('setting')->first();

        if ($request->hasFile('logo_app')) {
            $imgLogoName = 'logo-'.time().'.'.$request->file('logo_app')->extension();
            File::ensureDirectoryExists(public_path('img/app'));
            $request->file('logo_app')->move(public_path('img/app/'), $imgLogoName);
            if($setting->logo_app != "") {
                if (file_exists(public_path('img/app/' . $setting->logo_app))) {
                    unlink(public_path('img/app/' . $setting->logo_app));
                }
            }
        }
        if ($request->hasFile('image_bg_app')) {
            $imgBgName = 'bg-'.time().'.'.$request->file('image_bg_app')->extension();
            File::ensureDirectoryExists(public_path('img/app'));
            $request->file('image_bg_app')->move(public_path('img/app/'), $imgBgName);
            if($setting->image_bg_app != "") {
                if (file_exists(public_path('img/app/' . $setting->image_bg_app))) {
                    unlink(public_path('img/app/' . $setting->image_bg_app));
                }
            }
        }

        $post = $request->except(['_token', '_method', 'logo_app', 'image_bg_app']); // Use except to get all except token, method, and files
        
        // Convert checkbox values from 'on' to 1
        if (isset($post['image_bg_status']) && $post['image_bg_status'] === 'on') {
            $post['image_bg_status'] = 1;
        } else {
            $post['image_bg_status'] = 0;
        }
        
        if (isset($post['enable_rsvp']) && $post['enable_rsvp'] === 'on') {
            $post['enable_rsvp'] = 1;
        } else {
            $post['enable_rsvp'] = 0;
        }
        
        if (isset($post['enable_custom_qr']) && $post['enable_custom_qr'] === 'on') {
            $post['enable_custom_qr'] = 1;
        } else {
            $post['enable_custom_qr'] = 0;
        }
        
        // Handle any other boolean checkboxes here using the same pattern
        
        if ($request->hasFile('logo_app')) {
            $post['logo_app'] = $imgLogoName;
        }
        if ($request->hasFile('image_bg_app')) {
            $post['image_bg_app'] = $imgBgName;
        }

        DB::table('setting')->where(['id' => 1])->update($post);

        return redirect()->back()->with("success", "Data berhasil diupdate"); // Redirect back to setting.setting_app
    }

    public function emailTemplate()
    {
        $setting = \App\Models\Setting::first();
        
        // Create dummy data for template preview with properly escaped variables
        $guest = (object)[
            'name_guest' => '@{{ $guest->name_guest }}',
            'qr_code' => '@{{ $guest->qr_code }}'
        ];
        
        $invitation = (object)[
            'table_number_invitation' => '@{{ $invitation->table_number_invitation }}',
            'type_invitation' => '@{{ $invitation->type_invitation }}',
            'information_invitation' => '@{{ $invitation->information_invitation }}',
            'link_invitation' => '@{{ $invitation->link_invitation }}'
        ];

        return view('setting.setting_email_template', compact('setting', 'guest', 'invitation'));
    }

    public function emailTemplateUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_template_blasting' => 'nullable|string',
            'email_subject_template' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $post = $request->only(['email_template_blasting', 'email_subject_template']);

        DB::table('setting')->where(['id' => 1])->update($post);

        return redirect()->back()->with("success", "Email Template berhasil diupdate");
    }

    // Add RSVP settings section
    public function rsvpSettings()
    {
        $setting = DB::table('setting')->first();
        return view('setting.rsvp_settings', compact('setting'));
    }

    public function rsvpSettingsUpdate(Request $request)
    {
        // Log all request data for debugging
        \Log::info('RSVP Settings Update Request', [
            'all' => $request->all(),
            'has_enable_rsvp' => $request->has('enable_rsvp'),
            'enable_rsvp_value' => $request->input('enable_rsvp')
        ]);

        $validator = Validator::make($request->all(), [
            'enable_rsvp' => 'nullable',
            'rsvp_deadline' => 'nullable|date',
            'enable_plus_ones' => 'nullable',
            'collect_dietary_preferences' => 'nullable',
            'send_rsvp_reminders' => 'nullable',
            'reminder_days_before_deadline' => 'required|integer|min:1|max:30',
            'rsvp_email_template' => 'nullable|string',
            'rsvp_email_subject' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Build post data with explicit checkbox handling
        $post = [
            'reminder_days_before_deadline' => $request->input('reminder_days_before_deadline', 3),
            'rsvp_deadline' => $request->input('rsvp_deadline'),
            'rsvp_email_template' => $request->input('rsvp_email_template'),
            'rsvp_email_subject' => $request->input('rsvp_email_subject')
        ];
        
        // Set boolean fields explicitly, using input() to check for presence in form data
        $post['enable_rsvp'] = $request->input('enable_rsvp') ? 1 : 0;
        $post['enable_plus_ones'] = $request->input('enable_plus_ones') ? 1 : 0;
        $post['collect_dietary_preferences'] = $request->input('collect_dietary_preferences') ? 1 : 0;
        $post['send_rsvp_reminders'] = $request->input('send_rsvp_reminders') ? 1 : 0;

        // Log the actual data being saved
        \Log::info('RSVP Settings Being Saved', $post);

        DB::table('setting')->where(['id' => 1])->update($post);

        return redirect()->back()->with("success", "RSVP Settings berhasil diupdate");
    }
}

