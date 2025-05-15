<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'setting';

    protected $fillable = [
        'name_app',
        'logo_app',
        'color_bg_app',
        'image_bg_app',
        'image_bg_status',
        'send_email',
        'send_whatsapp',
        'greeting_page',
        'enable_rsvp',
        'rsvp_deadline',
        'enable_plus_ones',
        'collect_dietary_preferences',
        'send_rsvp_reminders',
        'reminder_days_before_deadline',
        'rsvp_email_template',
        'rsvp_email_subject',
        'email_template_blasting', // Added email template field
        'whatsapp_template_blasting', // Added whatsapp template field
    ];
}