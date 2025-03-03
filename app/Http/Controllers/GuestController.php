<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::orderBy('name_guest', 'ASC')
                    ->get();
        return view('guest.index', compact('guests'));
    }

    public function create()
    {
        return view('guest.create');
    }

    public function store(Request $request)
    {
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email',
      'phone' => 'required',
      // 'address' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()
        ->back()
        ->withErrors($validator)
        ->withInput();
    }

    Guest::create([
      "name_guest" => $request->name,
      "company_guest" => $request->company,
      "email_guest" => $request->email,
      "phone_guest" => $request->phone,
      "address_guest" => $request->address,
      "created_by_guest" => "admin",
      "custom_message" => $request->custom_message,
    ]);

    return redirect('/guest')->with('success', "Berhasil menambah data");
    }

    public function edit($id)
    {
        $guest = Guest::findOrFail($id);
        return view('guest.edit', compact('guest'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            // 'nik'         => [
            //     'required',
            //     Rule::unique('guest', 'nik_guest')->ignore($id, 'id_guest')
            // ],
      'name' => 'required',
      'email' => 'required|email',
      'phone' => 'required',
      'address' => 'required',
    ]);

    if ($validator->fails()) {
      return redirect()
        ->back()
        ->withErrors($validator)
        ->withInput();
    }

    Guest::where('id_guest', $id)->update([
      "name_guest" => $request->name,
      "company_guest" => $request->company,
      "email_guest" => $request->email,
      "phone_guest" => $request->phone,
      "address_guest" => $request->address,
      "created_by_guest" => "admin",
      "custom_message" => $request->custom_message,
    ]);

    return redirect('/guest')->with('success', "Berhasil mengedit data");
    }


    public function delete(Request $request)
    {
        $invitation = Invitation::where('id_guest', $request->id_guest)->get();
        foreach ($invitation as $key => $value) {
            if (file_exists(public_path('img/qrCode/' . $value->qrcode_invitation . ".png"))) {
                unlink(public_path('img/qrCode/' . $value->qrcode_invitation . ".png"));
            }
            if (file_exists(public_path('img/scan/scan-in/' . $value->qrcode_invitation . ".jpeg"))) {
                unlink(public_path('img/scan/scan-in/' . $value->qrcode_invitation . ".jpeg"));
            }   
            if (file_exists(public_path('img/scan/scan-out/' . $value->qrcode_invitation . ".jpeg"))) {
                unlink(public_path('img/scan/scan-out/' . $value->qrcode_invitation . ".jpeg"));
            }
        }
        Guest::where('id_guest', $request->id_guest)->delete();
        return redirect('guest')->with('success', "Berhasil menghapus data");
    }
}
