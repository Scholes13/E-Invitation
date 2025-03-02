<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $table = "event";
    protected $fillable = [
        'name_event',
        'type_event',
        'place_event',
        'location_event',
        'start_event',
        'end_event',
        'information_event',
        'image_event',
    ];

    protected $primaryKey = 'id_event';
}
