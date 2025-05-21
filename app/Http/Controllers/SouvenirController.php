<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Exports\SouvenirLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\QrCodeValidator;

class SouvenirController extends Controller
{
    use QrCodeValidator;

    /**
     * Display the souvenir scanning page.
     */
    public function index()
    {
        return view('souvenir.scan');
    }

    /**
     * Display the souvenir logs page.
     */
    public function logs()
    {
        $type = isset($_GET['type']) && $_GET['type'] != "" ? $_GET['type'] : "";
        $table = isset($_GET['table']) && $_GET['table'] != "" ? $_GET['table'] : "";

        $where = ['souvenir_claimed' => true];

        if ($type != "") {
            $where['type_invitation'] = $type;
        }
        
        if ($table != "") {
            $where['table_number_invitation'] = $table;
        }

        $invitations = Invitation::where($where)
            ->orderBy('souvenir_claimed_at', 'desc')
            ->get();

        return view('souvenir.logs', compact('invitations'));
    }

    /**
     * Process the souvenir claim.
     */
    public function processClaim(Request $request)
    {
        // Validate the QR code
        $qrcode = $this->validateQrCode($request->qrcode);
        
        // Check if QR code is valid
        if (!$qrcode) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format QR code tidak valid'
            ]);
        }

        $invt = Invitation::where('qrcode_invitation', $qrcode)->first();

        if (!$invt) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode tidak ditemukan'
            ]);
        }

        // Check if already claimed
        if ($invt->souvenir_claimed) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Souvenir sudah diambil pada ' . Carbon::parse($invt->souvenir_claimed_at)->format('d M Y H:i:s')
            ]);
        }

        // Process souvenir claim within a transaction to prevent race conditions
        try {
            $result = \DB::transaction(function() use ($invt, $request, $qrcode) {
                // Lock the record to prevent race conditions
                $invitation = Invitation::where('id_invitation', $invt->id_invitation)
                                      ->lockForUpdate()
                                      ->first();
                
                // Double-check if already claimed (could have changed after first check)
                if ($invitation->souvenir_claimed) {
                    return [
                        'status' => 'warning',
                        'message' => 'Souvenir sudah diambil pada ' . Carbon::parse($invitation->souvenir_claimed_at)->format('d M Y H:i:s')
                    ];
                }
                
                // Check if the guest has checked in (optional requirement)
                if ($invitation->checkin_invitation == null) {
                    return [
                        'status' => 'warning',
                        'message' => 'Tamu belum melakukan check-in'
                    ];
                }
                
                // Process souvenir claim
                $data = [
                    'souvenir_claimed' => true,
                    'souvenir_claimed_at' => Carbon::now(),
                ];
                
                // Save image if provided
                if ($file = $request->file('webcam')) {
                    $request->validate([
                        'webcam' => 'image|mimes:jpeg,png,jpg|max:2048'
                    ]);
                    
                    File::ensureDirectoryExists(public_path('img/scan/souvenir'));
                    $fileName = $qrcode . ".jpeg";
                    $file->move(public_path('img/scan/souvenir'), $fileName);
                    $data['souvenir_claimed_img'] = $fileName;
                }
                
                // Update invitation
                Invitation::where('id_invitation', $invitation->id_invitation)->update($data);
                
                return [
                    'status' => 'success',
                    'message' => 'Souvenir berhasil diklaim oleh ' . $invitation->name_guest
                ];
            }, 5); // 5 retries in case of deadlock
            
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Souvenir claim error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses klaim souvenir: Internal Server Error'
            ]);
        }
    }

    /**
     * Export souvenir claim logs to Excel.
     */
    public function export()
    {
        $type = isset($_GET['type']) && $_GET['type'] != "" ? $_GET['type'] : "";
        $table = isset($_GET['table']) && $_GET['table'] != "" ? $_GET['table'] : "";

        return (new \App\Exports\SouvenirLogsExport)->type($type)->table($table)->download('Log Souvenir.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
