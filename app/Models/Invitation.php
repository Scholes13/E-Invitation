<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $table = "invitation";
    protected $fillable = [
        "id_guest",
        "qrcode_invitation",
        "table_number_invitation",
        "type_invitation",
        "information_invitation",
        "link_invitation",
        "image_qrcode_invitation",
        "send_email_invitation",
        "checkin_invitation",
        "checkout_invitation",
    ];

    // public $timestamps = false;
}
