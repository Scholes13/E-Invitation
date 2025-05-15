<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGuest = Invitation::count();
        $totalInvitation = Invitation::count();
        $totalGuestCome = Invitation::whereNotNull('checkin_invitation')->count();
        $totalGuestNotYet = Invitation::whereNull('checkin_invitation')->count();
        $totalSouvenirClaimed = Invitation::where('souvenir_claimed', true)->count();
        $guestArrivals = Invitation::whereNotNull('checkin_invitation')
            ->orderBy('checkin_invitation', "DESC")
            ->limit(7)
            ->get();
        return view('dashboard.index', compact('guestArrivals','totalGuest', 'totalInvitation', 'totalGuestCome', 'totalGuestNotYet', 'totalSouvenirClaimed'));
    }
}
