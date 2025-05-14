<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SouvenirController extends Controller
{
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
        $invitations = Invitation::where('souvenir_claimed', true)
            ->orderBy('souvenir_claimed_at', 'desc')
            ->get();

        return view('souvenir.logs', compact('invitations'));
    }

    /**
     * Process the souvenir claim.
     */
    public function processClaim(Request $request)
    {
        $invt = Invitation::where('qrcode_invitation', $request->qrcode)->first();

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

        // Check if the guest has checked in (optional requirement)
        if ($invt->checkin_invitation == null) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Tamu belum melakukan check-in'
            ]);
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
            $fileName = $request->qrcode . ".jpeg";
            $file->move(public_path('img/scan/souvenir'), $fileName);
            $data['souvenir_claimed_img'] = $fileName;
        }

        // Update invitation
        Invitation::where('id_invitation', $invt->id_invitation)->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Souvenir berhasil diklaim oleh ' . $invt->name_guest
        ]);
    }

    /**
     * Export souvenir claim logs to Excel.
     */
    public function export()
    {
        $invitations = Invitation::where('souvenir_claimed', true)
            ->orderBy('souvenir_claimed_at', 'desc')
            ->get();

        // You can implement Excel export here similar to ArrivedController export
        // For now, just return a message
        return redirect()->back()->with('info', 'Export feature will be implemented soon.');
    }
}
