<?php
// Load framework
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Code yang ingin dieksekusi
$qrCode = 'i7x1xl'; // QR code yang ingin diregenerasi

echo "Memulai regenerasi QR code: {$qrCode}\n";

// Get template
$template = App\Models\CustomQrTemplate::where('is_default', true)->first();
echo "Template default: ID #{$template->id} ({$template->name})\n";

// Get invitation
$invitation = App\Models\Invitation::where('qrcode_invitation', $qrCode)->first();
if (!$invitation) {
    echo "Invitation dengan kode {$qrCode} tidak ditemukan\n";
    exit(1);
}

echo "Ditemukan invitation #{$invitation->id_invitation} untuk {$invitation->name_guest}\n";

// Generate QR code
$controller = new App\Http\Controllers\InvitationController();
$controller->qrcodeGenerator($qrCode, true);

echo "QR code berhasil digenerate\n";

// Update invitation
$invitation->custom_qr_template_id = $template->id;
$invitation->image_qrcode_invitation = '/img/qrCode/' . $qrCode . '.png';
$invitation->save();

echo "Invitation berhasil diupdate\n";

// Verify file
$path = public_path('img/qrCode/' . $qrCode . '.png');
if (file_exists($path)) {
    echo "File QR code ada di: {$path}\n";
    echo "Ukuran file: " . filesize($path) . " bytes\n";
    echo "Waktu modifikasi: " . date('Y-m-d H:i:s', filemtime($path)) . "\n";
} else {
    echo "File QR code tidak ada di: {$path}\n";
} 