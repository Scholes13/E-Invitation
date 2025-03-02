<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class prize extends Model
{
    use HasFactory;

    protected $table = 'doorprize_table'; // Table name
    protected $fillable = ['id_guest'];

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'id_guest', 'id_guest');
    }
}
