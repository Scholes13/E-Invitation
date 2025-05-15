<?php

// App\Http\Controllers\DoorprizeController.php
namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\DoorPrizeWinner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoorprizeController extends Controller
{
    public function index()
    {
        // Get previous winners for display
        $winners = DoorPrizeWinner::with('invitation')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($winner) {
                return (object)[
                    'id' => $winner->guest_id,
                    'name' => $winner->name,
                    'info' => optional($winner->invitation)->information_invitation,
                    'created_at' => $winner->created_at
                ];
            });
        
        return view('doorprize.index', compact('winners'));
    }
    
    public function spinWheel()
    {
        // Get all invitations that haven't won a door prize and have checked in
        $invitations = Invitation::whereDoesntHave('doorPrizeWinner')
            ->whereNotNull('checkin_invitation')
            ->get();
        
        // Get previous winners for display
        $previousWinners = DoorPrizeWinner::with('invitation')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('doorprize.wheel', compact('invitations', 'previousWinners'));
    }
    
    public function slotMachine()
    {
        // Get all invitations that haven't won a door prize and have checked in
        $invitations = Invitation::whereDoesntHave('doorPrizeWinner')
            ->whereNotNull('checkin_invitation')
            ->get();
        
        // Get previous winners for display
        $previousWinners = DoorPrizeWinner::with('invitation')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('doorprize.slots', compact('invitations', 'previousWinners'));
    }
    
    public function randomPick()
    {
        // Get all invitations that haven't won a door prize and have checked in
        $invitations = Invitation::whereDoesntHave('doorPrizeWinner')
            ->whereNotNull('checkin_invitation')
            ->get();
        
        // Get previous winners for display
        $previousWinners = DoorPrizeWinner::with('invitation')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('doorprize.random', compact('invitations', 'previousWinners'));
    }

    public function drawWinner()
    {
        // Use transaction to prevent race conditions
        DB::beginTransaction();
        
        try {
            // Get all invitations that haven't won a door prize and have checked in
            $invitations = Invitation::whereDoesntHave('doorPrizeWinner')
                ->whereNotNull('checkin_invitation')
                ->get();

            if ($invitations->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Semua tamu sudah menjadi pemenang atau belum ada tamu yang check-in.'
                ], 400);
            }

            // Select a random winner
            $winner = $invitations->random();

            // Save the winner to the database
            DoorPrizeWinner::create([
                'guest_id' => $winner->id_invitation,
                'name' => $winner->name_guest,
                'email' => $winner->email_guest
            ]);
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pemenang berhasil dipilih!',
                'winner' => [
                    'id' => $winner->id_invitation,
                    'name' => $winner->name_guest,
                    'info' => $winner->information_invitation,
                    'type' => $winner->type_invitation,
                    'table' => $winner->table_number_invitation
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getWinners()
    {
        $winners = DoorPrizeWinner::with('invitation')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($winner) {
                return [
                    'id' => $winner->guest_id,
                    'name' => $winner->name,
                    'info' => optional($winner->invitation)->information_invitation,
                    'type' => optional($winner->invitation)->type_invitation,
                    'table' => optional($winner->invitation)->table_number_invitation,
                    'time' => $winner->created_at->format('d M Y H:i:s')
                ];
            });
            
        return response()->json($winners);
    }
    
    public function resetWinners()
    {
        try {
            // Use transaction to ensure data integrity
            DB::beginTransaction();
            
            // Delete all winners from the door_prize_winners table - use delete() instead of truncate()
            DoorPrizeWinner::query()->delete();
            
            DB::commit();
            
            return redirect()->route('doorprize.index')->with('success', 'Semua data pemenang doorprize berhasil direset!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('doorprize.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getParticipants()
    {
        // Get all invitations that haven't won a door prize and have checked in
        $invitations = Invitation::whereDoesntHave('doorPrizeWinner')
            ->whereNotNull('checkin_invitation')
            ->get()
            ->map(function($invitation) {
                return [
                    'id' => $invitation->id_invitation,
                    'name' => $invitation->name_guest,
                    'info' => $invitation->information_invitation
                ];
            });
        
        return response()->json([
            'status' => 'success',
            'participants' => $invitations
        ]);
    }
}