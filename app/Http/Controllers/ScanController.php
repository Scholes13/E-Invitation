<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

// use App\Events\GreetingEvent;
use Pusher\Pusher;

class ScanController extends Controller
{
    protected function getPusherInstance()
    {
        return new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ]
        );
    }

    public function scanIn()
    {
        return view("scan.modern-qr");
    }

    public function scanInProcess(Request $request)
    {
        $invt = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
            ->where('qrcode_invitation',  $request->qrcode)
            ->select('invitation.*', 'guest.name_guest', 'guest.custom_message as guest_custom_message')
            ->first();

        if($invt) {
            // Periksa jika QR sudah pernah di-scan dalam waktu dekat (10 detik)
            $lastScanCheck = Invitation::where('id_invitation', $invt->id_invitation)
                ->where('last_scan_in', '>=', Carbon::now()->subSeconds(10))
                ->exists();
            
            if ($lastScanCheck) {
                return response()->json([
                    'status'    => "warning",
                    'message'   => "QR baru saja di-scan, harap tunggu beberapa saat"
                ]);
            }
            
            // Update last_scan_in timestamp untuk mencegah scan berulang
            Invitation::where('id_invitation', $invt->id_invitation)
                ->update(['last_scan_in' => Carbon::now()]);
            
            if($invt->checkin_invitation == null){
                $status = "success";
                $data['checkin_invitation'] = Carbon::now();
                if($file = $request->file('webcam')){
                    $request->validate([
                        'webcam' => 'image|mimes:jpeg,png,jpg|max:2048'
                    ]);

                    File::ensureDirectoryExists(public_path('img/scan/scan-in'));
                    $fileName = $request->qrcode . ".jpeg";
                    $file->move(public_path('img/scan/scan-in'), $fileName);
                    $data['checkin_img_invitation'] = $fileName;
                }
                Invitation::where('id_invitation', $invt->id_invitation)->update($data);
                $message = "Welcome : ".$invt->name_guest;
            } else {
                $status = "warning";
                $message = "Tamu sudah scan-in";
            }

            // event(new GreetingEvent('hello world'));
            // Konfigurasi Pusher
            $pusher = $this->getPusherInstance();
            // Kirim event ke channel "greetings" dengan event "new-scan"

            $data = [
                'intro' => 'Welcome',
                'guest' => $invt->name_guest,
                'meja' => $invt->table_number_invitation,
            ];

            if (!empty(trim($invt->guest_custom_message ?? ''))) {
                $data['custom_message'] = $invt->guest_custom_message;
            }

            $pusher->trigger('greetings', 'new-scan', $data);

        } else {
            $status = "error";
            $message = "Kode tidak ditemukan";
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message
        ]);
    }

    public function scanOut()
    {
        return view("scan.modern-qr-out");
    }
    
    public function scanOutProcess(Request $request)
    {
        $invt = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
            ->where('qrcode_invitation',  $request->qrcode)->first();

        if($invt){
            // Periksa jika QR sudah pernah di-scan dalam waktu dekat (10 detik)
            $lastScanCheck = Invitation::where('id_invitation', $invt->id_invitation)
                ->where('last_scan_out', '>=', Carbon::now()->subSeconds(10))
                ->exists();
            
            if ($lastScanCheck) {
                return response()->json([
                    'status'    => "warning",
                    'message'   => "QR baru saja di-scan, harap tunggu beberapa saat"
                ]);
            }
            
            // Update last_scan_out timestamp untuk mencegah scan berulang
            Invitation::where('id_invitation', $invt->id_invitation)
                ->update(['last_scan_out' => Carbon::now()]);
            
            $status = "warning";
            if($invt->checkin_invitation == null) {
                $message = "Tamu Belum Scan In";
            } else if($invt->checkout_invitation == null) {
                $status = "success";
                $data['checkout_invitation'] = Carbon::now();
                if($file = $request->file('webcam')){
                    $request->validate([
                        'webcam' => 'image|mimes:jpeg,png,jpg|max:2048'
                    ]);

                    File::ensureDirectoryExists(public_path('img/scan/scan-out'));
                    $fileName = $request->qrcode . ".jpeg";
                    $file->move(public_path('img/scan/scan-out'), $fileName);
                    $data['checkout_img_invitation'] = $fileName;
                }
                Invitation::where('id_invitation', $invt->id_invitation)->update($data);
                $message = $invt->name_guest.", terima kasih kehadirannya";
                
            } else {
                $status = "warning";
                $message = "Tamu sudah scan-out";
            }

            $pusher = $this->getPusherInstance();
            $pusher->trigger('greetings', 'new-scan', [
                'intro' => 'Thank you for coming',
                'guest' => $invt->name_guest,
            ]);

        } else {
            $status = "error";
            $message = "Kode tidak ditemukan";
        }

        return response()->json([
            'status'    => $status,
            'message'   => $message
        ]);
    }

    public function greeting()
    {
        return view("scan.greeting");
    }

}
