<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\Rule;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Color\Color;

class GuestController extends Controller
{
    public function index()
    {
        $guests = Guest::with('invitation')
                    ->orderBy('name_guest', 'ASC')
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

    $guest = Guest::create([
      "name_guest" => $request->name,
      "company_guest" => $request->company,
      "email_guest" => $request->email,
      "phone_guest" => $request->phone,
      "address_guest" => $request->address,
      "created_by_guest" => "admin",
      "custom_message" => $request->custom_message,
    ]);

    // Generate invitation for the new guest
    $qrcode = $this->generateCode();
    $this->qrcodeGenerator($qrcode);
    Invitation::create([
      "id_guest" => $guest->id_guest,
      "qrcode_invitation" => $qrcode,
      "type_invitation" => "reguler",
      "link_invitation" => '/invitation/' . $qrcode,
      "image_qrcode_invitation" => '/img/qrCode/' . $qrcode . ".png",
      "id_event" => 1,
    ]);

    return redirect('/guest')->with('success', "Berhasil menambah data dan membuat undangan");
    }

    // Add helper methods for QR code generation
    private function checkUniq($qrcode)
    {
        return Invitation::where('qrcode_invitation', $qrcode)->count() > 0;
    }

    private function generateCode()
    {
        $qrcode = strtoupper(substr(md5(time()), 0, 10));
        if ($this->checkUniq($qrcode)) {
            return $this->generateCode();
        }
        return $qrcode;
    }

    public function qrcodeGenerator($code)
    {
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $qrCode = \Endroid\QrCode\QrCode::create(url('/invitation/' . $code))
                    ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                    ->setErrorCorrectionLevel(new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow())
                    ->setSize(300)
                    ->setMargin(10)
                    ->setRoundBlockSizeMode(new \Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin())
                    ->setForegroundColor(new \Endroid\QrCode\Color\Color(0, 0, 0))
                    ->setBackgroundColor(new \Endroid\QrCode\Color\Color(255, 255, 255));

        $result = $writer->write($qrCode);
        $result->saveToFile(public_path('img/qrCode/' . $code . '.png'));
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

    // Check if this guest already has an invitation
    $hasInvitation = Invitation::where('id_guest', $id)->exists();
    
    // If not, create one
    if (!$hasInvitation) {
        $qrcode = $this->generateCode();
        $this->qrcodeGenerator($qrcode);
        Invitation::create([
            "id_guest" => $id,
            "qrcode_invitation" => $qrcode,
            "type_invitation" => "reguler",
            "link_invitation" => '/invitation/' . $qrcode,
            "image_qrcode_invitation" => '/img/qrCode/' . $qrcode . ".png",
            "id_event" => 1,
        ]);
        return redirect('/guest')->with('success', "Berhasil mengedit data dan membuat undangan baru");
    }

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
