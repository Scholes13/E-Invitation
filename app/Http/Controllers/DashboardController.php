<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGuest = Guest::count();
        $totalInvitation = Invitation::count();
        $totalGuestCome = Invitation::whereNotNull('invitation.checkin_invitation')->count();
        $totalGuestNotYet = Invitation::whereNull('invitation.checkin_invitation')->count();
        $guestArrivals = Guest::join('invitation', 'invitation.id_guest', '=', 'guest.id_guest')
            ->whereNotNull('invitation.checkin_invitation')
            ->orderBy('invitation.checkin_invitation', "DESC")
            ->limit(7)
            ->get();
        return view('dashboard.index', compact('guestArrivals','totalGuest', 'totalInvitation', 'totalGuestCome', 'totalGuestNotYet'));
    }
}
