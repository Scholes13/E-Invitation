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
        $setting = DB::table('setting')->get()->first();
        return view('setting.index', compact('setting'));
    }

    public function settingUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_app' => 'required|string|min:3|max:100',
            'logo_app' => 'nullable|image|max:512|mimes:jpg,jpeg,png',
            'image_bg_app' => 'nullable|image|max:1536|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $setting = DB::table('setting')->get()->first();

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

        $post['name_app'] = $request->name_app;
        if ($request->hasFile('logo_app')) {
            $post['logo_app'] = $imgLogoName;
        }
        if ($request->hasFile('image_bg_app')) {
            $post['image_bg_app'] = $imgBgName;
        }
        $post['color_bg_app'] = $request->color_bg_app;
        $post['image_bg_status'] = $request->image_bg_status ?? 0;
        DB::table('setting')->where(['id' => 1])->update($post);

        return redirect('setting')->with("success", "Data berhasil diupdate");

    }
}
