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
        return view("scan.scanIn");
    }

    public function scanInProcess(Request $request)
    {
        $invt = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
            ->where('qrcode_invitation',  $request->qrcode)
            ->select('invitation.*', 'guest.name_guest', 'guest.custom_message as guest_custom_message')
            ->first();

        if($invt) {
            if($invt->checkin_invitation == null){
                $status = "success";
                $data['checkin_invitation'] = Carbon::now();
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
