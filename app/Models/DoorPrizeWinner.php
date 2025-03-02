<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoorprizeWinner extends Model
{
    use HasFactory;

    protected $table = 'doorprizewinners'; // Match the table name in your database
    protected $fillable = ['id_guest'];

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'id_guest', 'id_guest');
    }
}
