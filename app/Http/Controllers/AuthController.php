<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth/login');
    }

    public function loginProcess(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'          => 'required',
            'password'          => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $username = $request->username;
        $password = $request->password;

        if(Auth::attempt(['username' => $username, 'password' => $password])){
            $request->session()->regenerate();
            return redirect('dashboard');
        }

        return redirect()->back()->with("error", "Username atau password salah!");

    }


    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/login');
    }

}
