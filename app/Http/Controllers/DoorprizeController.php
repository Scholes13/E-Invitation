<?php

// App\Http\Controllers\DoorprizeController.php
namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\DoorPrizeWinner; // Ganti dengan nama model yang benar
use Illuminate\Http\Request;

class DoorprizeController extends Controller
{
    public function index()
    {
        // Mengambil semua tamu yang sudah diundang dan belum menjadi pemenang
        $guests = Guest::with('invitation')
            ->whereHas('invitation')
            ->whereDoesntHave('doorPrizeWinner') // Pastikan tamu belum menjadi pemenang
            ->get();
        
        return view('doorprize.index', compact('guests'));
    }

    // App\Http\Controllers\DoorprizeController.php
public function drawWinner()
{
    // Mengambil semua tamu yang sudah diundang dan belum menjadi pemenang
    $guests = Guest::with('invitation')
        ->whereHas('invitation')
        ->whereDoesntHave('doorPrizeWinner') // Pastikan tamu belum menjadi pemenang
        ->get();

    if ($guests->isEmpty()) {
        return response()->json(['message' => 'Semua tamu sudah menjadi pemenang.'], 400);
    }

    // Mengundi pemenang
    $winner = $guests->random();

    // Menyimpan pemenang ke database tanpa guest_id
    DoorPrizeWinner::create([
        'name' => $winner->name_guest,
        'email' => $winner->email_guest // Jika Anda ingin menyimpan email
    ]);

    return response()->json(['winner' => $winner]);
}
}