<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $table = "invitations";
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
        "custom_qr_path",
        "send_email_invitation",
        "checkin_invitation",
        "checkout_invitation",
        "checkin_img_invitation",
        "checkout_img_invitation",
        "email_sent",
        "email_read",
        "email_bounced",
        "tracking_code",
        "last_tracked_at",
        "tracking_count",
        "tracking_client",
        "tracking_method",
        "last_scan_in",
        "last_scan_out",
        "souvenir_claimed",
        "souvenir_claimed_at",
        "souvenir_claimed_img",
    ];

    // public $timestamps = false;

    // DoorPrize relationship moved from Guest model
    public function doorPrizeWinner()
    {
        return $this->hasOne(DoorPrizeWinner::class, 'guest_id', 'id_invitation');
    }
}
