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
        'email_template_blasting', // Added email template field
        'whatsapp_template_blasting', // Added whatsapp template field
    ];
}