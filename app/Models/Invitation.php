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
        "name_guest",
        "email_guest",
        "phone_guest",
        "address_guest",
        "company_guest",
        "created_by_guest",
        "custom_message",
        "rsvp_status",
        "plus_ones_count",
        "plus_ones_names",
        "dietary_preferences",
        "rsvp_notes",
        "rsvp_responded_at",
        "rsvp_reminder_sent_at",
        "qrcode_invitation",
        "table_number_invitation",
        "type_invitation",
        "information_invitation",
        "link_invitation",
        "image_qrcode_invitation",
        "send_email_invitation",
        "checkin_invitation",
        "checkout_invitation",
        "email_sent",
        "email_read",
        "email_bounced",
        "last_scan_in",
        "last_scan_out",
    ];

    // public $timestamps = false;

    // DoorPrize relationship moved from Guest model
    public function doorPrizeWinner()
    {
        return $this->hasOne(DoorPrizeWinner::class, 'guest_id', 'id_invitation');
    }
}
