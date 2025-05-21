<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Checking database tables...\n";

// Check setting table
echo "\nSetting table:\n";
if (Schema::hasTable('setting')) {
    echo "Setting table exists\n";
    echo "Columns: " . implode(', ', Schema::getColumnListing('setting')) . "\n";
    
    // Check if enable_custom_qr column exists
    if (Schema::hasColumn('setting', 'enable_custom_qr')) {
        echo "enable_custom_qr column exists\n";
        
        // Check for settings data
        $settings = DB::table('setting')->first();
        if ($settings) {
            echo "Setting data found\n";
            echo "enable_custom_qr value: " . ($settings->enable_custom_qr ?? 'NULL') . "\n";
        } else {
            echo "No setting data found\n";
        }
    } else {
        echo "enable_custom_qr column does not exist\n";
    }
} else {
    echo "Setting table does not exist\n";
}

// Check custom_qr_templates table
echo "\nCustom QR Templates table:\n";
if (Schema::hasTable('custom_qr_templates')) {
    echo "custom_qr_templates table exists\n";
    echo "Columns: " . implode(', ', Schema::getColumnListing('custom_qr_templates')) . "\n";
    
    // Check template data
    $templates = DB::table('custom_qr_templates')->get();
    if ($templates->count() > 0) {
        echo "Found " . $templates->count() . " QR templates\n";
        foreach ($templates as $template) {
            echo "Template ID: {$template->id}, Name: {$template->name}\n";
        }
    } else {
        echo "No QR templates found\n";
    }
} else {
    echo "custom_qr_templates table does not exist\n";
}

echo "\nDone checking database tables\n"; 