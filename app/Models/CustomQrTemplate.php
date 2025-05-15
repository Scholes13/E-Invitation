<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomQrTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'fg_color',
        'bg_color',
        'secondary_color',
        'logo_path',
        'logo_size',
        'shape',
        'finder_pattern_style',
        'show_finder_pattern',
        'error_correction',
        'is_branded',
        'is_advanced_branded',
        'brand_theme',
        'sample_qr_path',
        'settings_json',
        'is_default',
    ];
    
    protected $casts = [
        'is_branded' => 'boolean',
        'show_finder_pattern' => 'boolean',
        'is_advanced_branded' => 'boolean',
        'is_default' => 'boolean',
    ];
    
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'custom_qr_template_id');
    }
} 