<?php
// Enhanced email testing script with automatic configuration detection

// Load composer autoload and Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use PHPMailer and Laravel
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

echo "=== EMAIL CONFIGURATION DEBUGGING ===\n";

// Get settings from database
$setting = \App\Models\Setting::first();
if (!$setting) {
    echo "WARNING: Settings record not found in database!\n";
}

// Collect config from various sources
$mail_config = [
    'driver' => env('MAIL_MAILER', Config::get('mail.default')),
    'host' => env('MAIL_HOST', Config::get('mail.mailers.smtp.host')),
    'port' => env('MAIL_PORT', Config::get('mail.mailers.smtp.port')),
    'username' => env('MAIL_USERNAME', Config::get('mail.mailers.smtp.username')),
    'password' => env('MAIL_PASSWORD', Config::get('mail.mailers.smtp.password')),
    'encryption' => env('MAIL_ENCRYPTION', Config::get('mail.mailers.smtp.encryption')),
    'from_address' => env('MAIL_FROM_ADDRESS', Config::get('mail.from.address')),
    'from_name' => env('MAIL_FROM_NAME', Config::get('mail.from.name'))
];

// Print config (with password hidden)
echo "Using mail configuration:\n";
echo "MAIL_DRIVER: " . $mail_config['driver'] . "\n";
echo "MAIL_HOST: " . $mail_config['host'] . "\n";
echo "MAIL_PORT: " . $mail_config['port'] . "\n";
echo "MAIL_USERNAME: " . $mail_config['username'] . "\n";
echo "MAIL_PASSWORD: " . (empty($mail_config['password']) ? "NOT SET" : "********") . "\n";
echo "MAIL_ENCRYPTION: " . $mail_config['encryption'] . "\n";
echo "MAIL_FROM_ADDRESS: " . $mail_config['from_address'] . "\n";
echo "MAIL_FROM_NAME: " . $mail_config['from_name'] . "\n\n";

// Save to log
Log::info("Testing email with settings", [
    'host' => $mail_config['host'],
    'port' => $mail_config['port'],
    'username' => $mail_config['username'],
    'from' => $mail_config['from_address']
]);

// Get recipients from database
$recipients = \App\Models\Invitation::whereNotNull('email_guest')
    ->take(1)
    ->get(['name_guest', 'email_guest', 'qrcode_invitation']);

if ($recipients->isEmpty()) {
    echo "No recipients found in database. Adding test recipient.\n";
    $recipients = [[
        'name_guest' => 'Test User',
        'email_guest' => 'test@example.com',
        'qrcode_invitation' => 'TEST123'
    ]];
} else {
    echo "Found recipient in database: " . $recipients[0]->email_guest . "\n";
}

// Test with PHPMailer directly
echo "=== TESTING PHPMAILER WITH YOUR SETTINGS ===\n";

try {
    // Create PHPMailer instance
    $mail = new PHPMailer(true);
    
    // Show debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
    $mail->Debugoutput = function($str, $level) {
        echo "Debug ($level): $str\n";
    };
    
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = $mail_config['host'];
    $mail->Port = $mail_config['port'];
    $mail->SMTPAuth = !empty($mail_config['username']);
    $mail->Username = $mail_config['username'];
    $mail->Password = $mail_config['password'];
    $mail->SMTPSecure = $mail_config['encryption'] ?? '';
    
    // Sender and recipient
    $mail->setFrom($mail_config['from_address'], $mail_config['from_name']);
    
    // Add all recipients
    foreach ($recipients as $recipient) {
        $email = $recipient->email_guest ?? $recipient['email_guest'];
        $name = $recipient->name_guest ?? $recipient['name_guest'];
        echo "Adding recipient: $name <$email>\n";
        $mail->addAddress($email, $name);
    }
    
    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - ' . date('Y-m-d H:i:s');
    
    // Use the same template as in the app
    if ($setting && !empty($setting->email_template_blasting)) {
        echo "Using email template from database\n";
        $template = $setting->email_template_blasting;
        
        // Replace variables
        foreach ($recipients as $recipient) {
            $code = $recipient->qrcode_invitation ?? $recipient['qrcode_invitation'];
            $name = $recipient->name_guest ?? $recipient['name_guest'];
            
            $template = str_replace('{qrcode}', $code, $template);
            $template = str_replace('{name}', $name, $template);
        }
        
        $mail->Body = $template;
    } else {
        echo "Using default test template\n";
        $mail->Body = '<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #3498db; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test Email</h1>
        <p>This is a test email sent using PHPMailer with SMTP.</p>
        <p>If you received this, your SMTP settings are working!</p>
    </div>
</body>
</html>';
    }

    // Attempt to send
    $mail->send();
    echo "\nMessage sent successfully!\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "Error code: " . (isset($mail) ? $mail->ErrorInfo : "Unknown") . "\n";
    
    echo "\n=== TRYING ALTERNATIVE METHOD ===\n";
    try {
        // Try using Laravel's Mail facade
        echo "Testing with Laravel Mail facade...\n";
        
        $recipient = $recipients[0];
        $email = $recipient->email_guest ?? $recipient['email_guest'];
        $name = $recipient->name_guest ?? $recipient['name_guest'];
        
        $invitation = new \StdClass();
        $invitation->name_guest = $name;
        $invitation->email_guest = $email;
        $invitation->qrcode_invitation = $recipient->qrcode_invitation ?? $recipient['qrcode_invitation'];
        
        $subject = "Test Email - Laravel - " . date('Y-m-d H:i:s');
        
        // Use same template
        $template = $setting ? $setting->email_template_blasting : null;
        if ($template) {
            $template = str_replace('{qrcode}', $invitation->qrcode_invitation, $template);
            $template = str_replace('{name}', $invitation->name_guest, $template);
        }
        
        \Illuminate\Support\Facades\Mail::to($email)
            ->send(new \App\Mail\InvitationMail($invitation, $subject, $template));
            
        echo "Laravel Mail appears to have been sent successfully.\n";
    } catch (Exception $e2) {
        echo "Laravel Mail failed too: " . $e2->getMessage() . "\n";
        
        echo "\n=== TRYING DIRECT PHP MAIL ===\n";
        try {
            echo "Testing with PHP mail() function...\n";
            $mail2 = new PHPMailer(true);
            $mail2->isMail(); // Use PHP's mail() function
            $mail2->setFrom($mail_config['from_address'], $mail_config['from_name']);
            
            $recipient = $recipients[0];
            $email = $recipient->email_guest ?? $recipient['email_guest'];
            $name = $recipient->name_guest ?? $recipient['name_guest'];
            
            $mail2->addAddress($email, $name);
            $mail2->isHTML(true);
            $mail2->Subject = 'Test Email - PHP mail() - ' . date('Y-m-d H:i:s');
            $mail2->Body = '<h1>Test Email</h1><p>This is a test using PHP mail().</p>';
            
            $mail2->send();
            echo "PHP mail() method successful!\n";
        } catch (Exception $e3) {
            echo "All email methods failed!\n";
            echo "PHP mail() error: " . $e3->getMessage() . "\n";
        }
    }
}

echo "\n=== TEST COMPLETE ===\n"; 