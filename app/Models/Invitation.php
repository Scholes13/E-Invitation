<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $table = "invitation";
    protected $primaryKey = 'id_invitation';
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
        "custom_message",
        "email_sent",
        "email_read",
        "email_bounced",
    ];

    // public $timestamps = false;

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'id_guest', 'id_guest');
    }
}
