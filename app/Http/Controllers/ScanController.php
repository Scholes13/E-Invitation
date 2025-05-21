<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Http\Traits\QrCodeValidator;

// use App\Events\GreetingEvent;
use Pusher\Pusher;

class ScanController extends Controller
{
    use QrCodeValidator;

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
        try {
            // Get and validate the QR code from the request
            $qrcode = $this->validateQrCode($request->qrcode);
            
            // Check if QR code is valid
            if (!$qrcode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format QR code tidak valid'
                ]);
            }
            
            // Mulai transaction dan gunakan locking untuk mencegah race condition
            $result = \DB::transaction(function() use ($qrcode, $request) {
                // Find the invitation by QR code with locking
                $invt = Invitation::where('qrcode_invitation', $qrcode)->lockForUpdate()->first();
                
                if(!$invt) {
                    return [
                        'status' => "error",
                        'message' => "Kode tidak ditemukan"
                    ];
                }
                
                // Periksa jika QR sudah pernah di-scan dalam waktu dekat (10 detik)
                if ($invt->last_scan_in && Carbon::parse($invt->last_scan_in)->diffInSeconds(Carbon::now()) < 10) {
                    return [
                        'status' => "warning",
                        'message' => "QR baru saja di-scan, harap tunggu beberapa saat"
                    ];
                }
                
                // Update last_scan_in timestamp untuk mencegah scan berulang
                $invt->last_scan_in = Carbon::now();
                $invt->save();
                
                if($invt->checkin_invitation == null){
                    $status = "success";
                    $data['checkin_invitation'] = Carbon::now();
                    if($file = $request->file('webcam')){
                        $request->validate([
                            'webcam' => 'image|mimes:jpeg,png,jpg|max:2048'
                        ]);

                        File::ensureDirectoryExists(public_path('img/scan/scan-in'));
                        $fileName = $qrcode . ".jpeg";
                        $file->move(public_path('img/scan/scan-in'), $fileName);
                        $data['checkin_img_invitation'] = $fileName;
                    }
                    Invitation::where('id_invitation', $invt->id_invitation)->update($data);
                    $message = "Welcome : ".$invt->name_guest;
                    
                    // Konfigurasi Pusher
                    $pusher = $this->getPusherInstance();
                    // Kirim event ke channel "greetings" dengan event "new-scan"

                    $data = [
                        'intro' => 'Welcome',
                        'guest' => $invt->name_guest,
                        'meja' => $invt->table_number_invitation,
                    ];

                    if (!empty(trim($invt->custom_message ?? ''))) {
                        $data['custom_message'] = $invt->custom_message;
                    }

                    $pusher->trigger('greetings', 'new-scan', $data);
                    
                } else {
                    $status = "warning";
                    $message = "Tamu sudah scan-in";
                }
                
                return [
                    'status' => $status,
                    'message' => $message
                ];
            }, 5); // Retry 5 times if deadlock occurs

            return response()->json($result);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Scan error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Return error response
            return response()->json([
                'status'    => "error",
                'message'   => "Scan gagal: Internal Server Error"
            ]);
        }
    }

    public function scanOut()
    {
        return view("scan.modern-qr-out");
    }
    
    public function scanOutProcess(Request $request)
    {
        try {
            // Get and validate the QR code from the request
            $qrcode = $this->validateQrCode($request->qrcode);
            
            // Check if QR code is valid
            if (!$qrcode) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format QR code tidak valid'
                ]);
            }
            
            // Mulai transaction dan gunakan locking untuk mencegah race condition
            $result = \DB::transaction(function() use ($qrcode, $request) {
                // Find the invitation by QR code with locking
                $invt = Invitation::where('qrcode_invitation', $qrcode)->lockForUpdate()->first();
                
                if(!$invt) {
                    return [
                        'status' => "error",
                        'message' => "Kode tidak ditemukan"
                    ];
                }
                
                // Periksa jika QR sudah pernah di-scan dalam waktu dekat (10 detik)
                if ($invt->last_scan_out && Carbon::parse($invt->last_scan_out)->diffInSeconds(Carbon::now()) < 10) {
                    return [
                        'status' => "warning",
                        'message' => "QR baru saja di-scan, harap tunggu beberapa saat"
                    ];
                }
                
                // Update last_scan_out timestamp untuk mencegah scan berulang
                $invt->last_scan_out = Carbon::now();
                $invt->save();
                
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
                        $fileName = $qrcode . ".jpeg";
                        $file->move(public_path('img/scan/scan-out'), $fileName);
                        $data['checkout_img_invitation'] = $fileName;
                    }
                    Invitation::where('id_invitation', $invt->id_invitation)->update($data);
                    $message = $invt->name_guest.", terima kasih kehadirannya";
                    
                    $pusher = $this->getPusherInstance();
                    $pusher->trigger('greetings', 'new-scan', [
                        'intro' => 'Thank you for coming',
                        'guest' => $invt->name_guest,
                    ]);
                    
                } else {
                    $status = "warning";
                    $message = "Tamu sudah scan-out";
                }
                
                return [
                    'status' => $status,
                    'message' => $message
                ];
            }, 5); // Retry 5 times if deadlock occurs

            return response()->json($result);
            
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Scan out error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Return error response
            return response()->json([
                'status'    => "error",
                'message'   => "Scan gagal: Internal Server Error"
            ]);
        }
    }

    public function greeting()
    {
        return view("scan.greeting");
    }

    public function scanVerify($code)
    {
        try {
            // Validate the QR code format first
            $code = $this->validateQrCode($code);
            
            // Check if QR code is valid
            if (!$code) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Format QR code tidak valid'
                ]);
            }
            
            // First check if the code exists in the database
            $invitation = Invitation::where('qrcode_invitation', $code)->first();
            
            if (!$invitation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode QR tidak ditemukan'
                ]);
            }
            
            // Return the code directly - this is what will be processed by the scan/in-process endpoint
            return response()->json([
                'status' => 'success',
                'code' => $code,
                'name' => $invitation->name_guest
            ]);
        } catch (\Exception $e) {
            \Log::error('Scan verify error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memverifikasi kode QR'
            ]);
        }
    }

}
