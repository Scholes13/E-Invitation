<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $table = "guest";
    protected $primaryKey = "id_guest";
    protected $fillable = [
        "name_guest",
        "address_guest",
        "information_guest",
        "email_guest",
        "phone_guest",
        "nik_guest",
    ];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class, 'id_guest', 'id_guest');
    }

    // Relasi dengan DoorPrizeWinner
    public function doorPrizeWinner()
    {
        return $this->hasOne(DoorPrizeWinner::class, 'guest_id', 'id_guest');
    }
}