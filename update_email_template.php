<?php
// Script to update email template in database

// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Updating email template in database...\n";

try {
    // Get settings from database
    $setting = \App\Models\Setting::first();
    
    if (!$setting) {
        echo "ERROR: Settings record not found in database!\n";
        exit(1);
    }
    
    echo "Current template: " . (empty($setting->email_template_blasting) ? "EMPTY" : "EXISTS") . "\n";
    
    // Create a proper HTML template
    $html_template = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Undangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <p>Dear {name},</p>
        
        <p>We are delighted to invite you to our event.</p>
        
        <p>Your personal invitation code is: {qrcode}</p>
        
        <p>Thank You</p>
    </div>
</body>
</html>';

    // Update the setting
    $setting->email_template_blasting = $html_template;
    $setting->save();
    
    echo "Template updated successfully!\n";
    echo "Now try sending emails again.\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} 