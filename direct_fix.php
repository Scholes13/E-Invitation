<?php
// Load framework
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Kode QR yang ingin difix
$qrCode = 'i7x1xl';
echo "Memperbaiki QR code: $qrCode\n";

// Ambil template default
$template = App\Models\CustomQrTemplate::where('is_default', true)->first();
echo "Template default: #{$template->id} ({$template->name})\n";

// Ambil path logo
$logoPath = null;
if ($template->logo_path) {
    $logoPath = \Illuminate\Support\Facades\Storage::path($template->logo_path);
    echo "Logo path: $logoPath\n";
    
    // Periksa file logo
    if (file_exists($logoPath)) {
        echo "Logo file exists: " . filesize($logoPath) . " bytes\n";
    } else {
        echo "Logo file tidak ada!\n";
        exit(1);
    }
} else {
    echo "Template tidak memiliki logo\n";
    exit(1);
}

// Buat QR code base
echo "Membuat QR code base\n";
$qrCode = \Endroid\QrCode\QrCode::create($qrCode)
    ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
    ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
    ->setSize(300)
    ->setMargin(10);

// Atur warna foreground (hitam)
$qrCode->setForegroundColor(new \Endroid\QrCode\Color\Color(0, 0, 0));

// Atur warna background (putih)
$qrCode->setBackgroundColor(new \Endroid\QrCode\Color\Color(255, 255, 255));

// Buat writer
$writer = new \Endroid\QrCode\Writer\PngWriter();

// Tambahkan logo
echo "Menambahkan logo ke QR code\n";
$logo = \Endroid\QrCode\Logo\Logo::create($logoPath)
    ->setResizeToWidth(80); // Ukuran logo

$result = $writer->write($qrCode, $logo);

// Simpan QR code
$qrPath = public_path('img/qrCode/i7x1xl.png');
echo "Menyimpan QR code ke: $qrPath\n";
file_put_contents($qrPath, $result->getString());

// Update invitation
$invitation = App\Models\Invitation::where('qrcode_invitation', 'i7x1xl')->first();
if ($invitation) {
    $invitation->custom_qr_template_id = $template->id;
    $invitation->image_qrcode_invitation = '/img/qrCode/i7x1xl.png';
    $invitation->save();
    echo "Invitation #{$invitation->id_invitation} diupdate\n";
}

// Verifikasi file QR code
if (file_exists($qrPath)) {
    echo "QR code berhasil dibuat: $qrPath\n";
    echo "Ukuran file: " . filesize($qrPath) . " bytes\n";
    echo "Waktu modifikasi: " . date('Y-m-d H:i:s', filemtime($qrPath)) . "\n";
} else {
    echo "QR code gagal dibuat\n";
}

echo "\nProses selesai. Buka http://localhost:8000/invitation/i7x1xl dan refresh browser (Ctrl+F5).\n"; 