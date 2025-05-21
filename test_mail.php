<?php
// Basic email test script using Laravel's framework

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\InvitationMail;
use App\Models\Invitation;

// Create a mock invitation object
$invitation = new \StdClass();
$invitation->name_guest = 'Test User';
$invitation->email_guest = 'test@example.com'; // Replace with your test email
$invitation->qrcode_invitation = 'TEST123';

// Test mail configuration
echo "Mail configuration:\n";
echo "MAIL_MAILER: " . env('MAIL_MAILER') . "\n";
echo "MAIL_HOST: " . env('MAIL_HOST') . "\n";
echo "MAIL_PORT: " . env('MAIL_PORT') . "\n";
echo "MAIL_USERNAME: " . (env('MAIL_USERNAME') ? 'Set' : 'Not set') . "\n";
echo "MAIL_PASSWORD: " . (env('MAIL_PASSWORD') ? 'Set' : 'Not set') . "\n";
echo "MAIL_ENCRYPTION: " . env('MAIL_ENCRYPTION') . "\n";
echo "MAIL_FROM_ADDRESS: " . env('MAIL_FROM_ADDRESS') . "\n";
echo "MAIL_FROM_NAME: " . env('MAIL_FROM_NAME') . "\n\n";

try {
    echo "Trying to send a test email...\n";
    
    // Create simple HTML content
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <title>Test Email</title>
    </head>
    <body>
        <h1>Test Email</h1>
        <p>This is a test email to verify SMTP settings.</p>
        <p>If you received this, email sending is working correctly!</p>
    </body>
    </html>';
    
    // Try sending a simple email using PHP's mail function
    echo "Testing mail() function...\n";
    $success = mail('test@example.com', 'PHP Mail Test', 'This is a test email from PHP mail() function.');
    echo "mail() function result: " . ($success ? 'Success' : 'Failed') . "\n\n";
    
    // Try sending using PHPMailer directly
    echo "Testing PHPMailer...\n";
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = env('MAIL_HOST');
    $mail->Port = env('MAIL_PORT');
    $mail->SMTPAuth = true;
    $mail->Username = env('MAIL_USERNAME');
    $mail->Password = env('MAIL_PASSWORD');
    $mail->SMTPSecure = env('MAIL_ENCRYPTION');
    $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    $mail->addAddress('test@example.com');
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test';
    $mail->Body = $html;
    $mail->send();
    echo "PHPMailer: Message sent successfully\n\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "Test script completed\n"; 