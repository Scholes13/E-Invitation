<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email',
            'username'      => 'required',
            'password'      => 'required',
            'role'          => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }


        User::create([
            "name"              => $request->name,
            "email"             => $request->email,
            "username"          => $request->username,
            "password"          => Hash::make($request->password),
            "role"              => $request->role,
            "information"       => $request->information,
        ]);


        return redirect('/user')->with('success', "Berhasil menambah data");
    }

    public function edit($id)
    {
        $user = User::where('id', $id)->first();
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => [
                'required',
                Rule::unique('users', 'email')->ignore($id, 'id')
            ],
            'username'      => 'required',
            'role'          => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            "name"              => $request->name,
            "email"             => $request->email,
            "username"          => $request->username,
            "role"              => $request->role,
            "information"       => $request->information,
        ];

        if($request->password != ""){
            $data['password'] = Hash::make($request->password);
        }


        User::where('id', $id)->update($data);

        return redirect('/user')->with('success', "Berhasil mengedit data");
    }


    public function delete(Request $request)
    {
        if($request->id != 1){
            User::where('id', $request->id)->delete();
            return redirect('user')->with('success', "Berhasil menghapus data");
        }else{
            return redirect('user')->with('warning', "Data admin tidak bisa dihapus");
        }
    }



    public function profile()
    {
        return view('user.profile');
    }

    public function changePassword()
    {
        return view('user.changePassword');
    }

    public function changePasswordProcess(Request $request)
    {
        $status = "error";
        $redirect = url('change-password');
        $oldpass = $request->old_pass;
        $newpass = $request->new_pass;
        $passconf = $request->pass_conf;

        $user = Auth::user();

        if(Hash::check($oldpass, $user->password)){
            if($newpass === $passconf){
                User::where('id', $user->id)->update(['password' => Hash::make($newpass)]);
                $status = "success";
                $message = "Password berhasil diganti";
                $redirect = url('user-profile');
            }else{
                $message = "Konfirmasi password tidak sama";
            }
        }else{
            $message = "Password lama tidak sesuai";
        }

        return redirect($redirect)->with($status, $message);


    }

}
