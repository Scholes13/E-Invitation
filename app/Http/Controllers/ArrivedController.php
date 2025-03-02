<?php

namespace App\Http\Controllers;

use App\Exports\ArrivalLogExport;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ArrivedController extends Controller
{

    public function index()
    {
        $invitations = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
                            ->orderBy('checkout_invitation', "desc")
                            ->orderBy('checkin_invitation', "desc")
                            ->get();
        return view('arrived.index', compact('invitations'));
    }


    public function processScan(Request $request)
    {

        if($request->come == 1){
            $data['checkout_invitation'] = Carbon::now();
        } else {
            $data['checkin_invitation'] = Carbon::now();
        }

        Invitation::where('id_invitation', $request->id)->update($data);
        return redirect('/arrived-manually')->with('success', "Scan Manual Berhasil");
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

        $type != "" ? $where['type_invitation'] = $type : "";
        $table != "" ? $where['table_number_invitation'] = $table : "";

        $invt = Invitation::whereNotNull('invitation.checkin_invitation')
                        ->join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
                        ->where($where)
                        ->orderBy('invitation.checkin_invitation', "DESC")
                        ->get();

        $paramsUrl = "?type=" . $type;
        
        return view('arrival-log.index', compact('invt', 'paramsUrl'));
    }

    public function arrivalLogDetail($id)
    {
        $invt = Invitation::where('id_invitation', $id)
                            ->join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
                            ->first();
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
