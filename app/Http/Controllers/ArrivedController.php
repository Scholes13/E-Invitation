<?php

namespace App\Http\Controllers;

use App\Exports\ArrivalLogExport;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Traits\QrCodeValidator;

class ArrivedController extends Controller
{
    use QrCodeValidator;

    public function index()
    {
        $invitations = Invitation::orderBy('checkout_invitation', "desc")
                            ->orderBy('checkin_invitation', "desc")
                            ->get();
        return view('arrived.index', compact('invitations'));
    }


    public function processScan(Request $request)
    {
        try {
            // Validate input parameters
            $id = $request->id;
            $come = $request->come;
            
            if (empty($id)) {
                return redirect('/arrived-manually')->with('error', "ID undangan tidak valid");
            }
            
            if (!in_array($come, [0, 1])) {
                return redirect('/arrived-manually')->with('error', "Parameter 'come' tidak valid");
            }
            
            // Process within transaction for consistency
            \DB::transaction(function() use ($id, $come) {
                // Lock the record for update to prevent race conditions
                $invitation = Invitation::where('id_invitation', $id)->lockForUpdate()->first();
                
                if (!$invitation) {
                    return redirect('/arrived-manually')->with('error', "Undangan tidak ditemukan");
                }
                
                if($come == 1){
                    $data['checkout_invitation'] = \Carbon\Carbon::now();
                } else {
                    $data['checkin_invitation'] = \Carbon\Carbon::now();
                }
                
                Invitation::where('id_invitation', $id)->update($data);
            });
            
            return redirect('/arrived-manually')->with('success', "Scan Manual Berhasil");
        } catch (\Exception $e) {
            \Log::error('Arrived process scan error: ' . $e->getMessage());
            return redirect('/arrived-manually')->with('error', "Terjadi kesalahan: " . $e->getMessage());
        }
    }


    public function arrivalLogExport()
    {
        $type = isset($_GET['type']) && $_GET['type'] != "" ? $_GET['type'] : "";
        $table = isset($_GET['table']) && $_GET['table'] != "" ? $_GET['table'] : "";

        return (new ArrivalLogExport)->type($type)->table($table)->download('Log Kedatangan.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function arrivalLog()
    {
        $type = isset($_GET['type']) && $_GET['type'] != "" ? $_GET['type'] : "";
        $table = isset($_GET['table']) && $_GET['table'] != "" ? $_GET['table'] : "";

        $where = [];

        if ($type != "") {
            $where['type_invitation'] = $type;
        }
        
        if ($table != "") {
            $where['table_number_invitation'] = $table;
        }

        $invt = Invitation::whereNotNull('checkin_invitation')
                        ->where($where)
                        ->orderBy('checkin_invitation', "DESC")
                        ->get();

        $paramsUrl = "?type=" . $type;
        if ($table != "") {
            $paramsUrl .= "&table=" . $table;
        }
        
        return view('arrival-log.index', compact('invt', 'paramsUrl'));
    }

    public function arrivalLogDetail($id)
    {
        $invt = Invitation::where('id_invitation', $id)->first();
        return view('arrival-log.detail', compact('invt'));
    }

    public function deleteAllLogs()
    {
    try {
        // Menghapus data checkin dan checkout pada semua undangan
        Invitation::query()->update([
            'checkin_invitation' => null,
            'checkout_invitation' => null,
        ]);

        // Redirect dengan pesan sukses
        return redirect('/arrival-log')->with('success', 'Semua log kedatangan berhasil dihapus.');
    } catch (\Exception $e) {
        // Redirect dengan pesan error jika terjadi kesalahan
        return redirect('/arrival-log')->with('error', 'Terjadi kesalahan saat menghapus log kedatangan: ' . $e->getMessage());
    }
    }


}
