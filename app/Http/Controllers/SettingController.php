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
}

