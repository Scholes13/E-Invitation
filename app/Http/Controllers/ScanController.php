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
    public function scanIn()
    {
        return view("scan.scanIn");
    }

    public function scanInProcess(Request $request)
    {
        $invt = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
            ->where('qrcode_invitation',  $request->qrcode)->first();

        if($invt) {
            if($invt->checkin_invitation == null){
                $status = "success";
                $data['checkin_invitation'] = Carbon::now();
                if($file = $request->file('webcam')){
                    File::ensureDirectoryExists(public_path('img/scan/scan-in'));
                    $file->move(public_path('img/scan/scan-in'), $request->qrcode . ".jpeg" );
                    $data['checkin_img_invitation'] = $request->qrcode . ".jpeg";
                }
                Invitation::where('id_invitation', $invt->id_invitation)->update($data);
                $message = "Welcome : ".$invt->name_guest;
            } else {
                $status = "warning";
                $message = "Tamu sudah scan-in";
            }

              // Menyesuaikan pesan berdasarkan nama tamu
            if ($invt->name_guest == "Dwi Krismiyanto") {
                $pesanku = "Kayune mana Wik???";
                $invt->table_number_invitation;
            } elseif ($invt->name_guest == "Alya Widuri Pelitahati") {
                $pesanku = "Are you a magician? Because whenever I look at you, everyone else disappears.";
                $aipoto = 'https://prawam.maharajapratama.com/images/labubu.png';
                $invt->table_number_invitation;
            } elseif ($invt->name_guest == "Adiel Priyarama") {
                $pesanku = "Do you have a map? Because I keep getting lost in your eyes.";
                $aipoto = 'https://prawam.maharajapratama.com/images/Adiel.png';
                $invt->table_number_invitation;
            } else {
                $pesanku = "Welcome, " . $invt->name_guest . "!";
                $aipoto = 'https://prawam.maharajapratama.com/images/default.png';
                $invt->table_number_invitation;
            }

            // event(new GreetingEvent('hello world'));
            // Konfigurasi Pusher
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true,
                ]
            );
            // Kirim event ke channel "greetings" dengan event "new-scan"
            $pusher->trigger('greetings', 'new-scan', [
                'intro' => 'Welcome',
                'guest' => $invt->name_guest,   
                'pesanku' => $pesanku, // pesan pribadi
                'aipoto' => $aipoto,
                'meja' => $invt->table_number_invitation,
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

    public function scanOut()
    {
        return view("scan.scanOut");
    }
    
    public function scanOutProcess(Request $request)
    {
        $invt = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
            ->where('qrcode_invitation',  $request->qrcode)->first();

        if($invt){
            $status = "warning";
            if($invt->checkin_invitation == null) {
                $message = "Tamu Belum Scan In";
            } else if($invt->checkout_invitation == null) {
                $status = "success";
                $data['checkout_invitation'] = Carbon::now();
                if($file = $request->file('webcam')){
                    File::ensureDirectoryExists(public_path('img/scan/scan-out'));
                    $file->move(public_path('img/scan/scan-out')  , $request->qrcode . ".jpeg" );
                    $data['checkout_img_invitation'] = $request->qrcode . ".jpeg";
                }
                Invitation::where('id_invitation', $invt->id_invitation)->update($data);
                $message = $invt->name_guest.", terima kasih kehadirannya";
                
            } else {
                $status = "warning";
                $message = "Tamu sudah scan-out";
            }

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                [
                    'cluster' => env('PUSHER_APP_CLUSTER'),
                    'useTLS' => true,
                ]
            );
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
