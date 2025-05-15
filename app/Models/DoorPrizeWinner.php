<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoorprizeWinner extends Model
{
    use HasFactory;

    protected $table = 'doorprizewinners'; // Match the table name in your database
    protected $fillable = ['guest_id', 'name', 'email'];

    public function invitation()
    {
        return $this->belongsTo(Invitation::class, 'guest_id', 'id_invitation');
    }
}
