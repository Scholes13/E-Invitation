<?php
// Load framework
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Langkah 1: Generate QR code mentah tanpa logo untuk membuat baseline
$qrCode = 'i7x1xl';
echo "Membuat baseline QR code untuk $qrCode\n";

// Generate QR code dasar menggunakan Endroid QR directly
$qrData = $qrCode;
$result = \Endroid\QrCode\Builder\Builder::create()
    ->writer(new \Endroid\QrCode\Writer\PngWriter())
    ->writerOptions([])
    ->data($qrData)
    ->encoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
    ->errorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
    ->size(300)
    ->margin(10)
    ->roundBlockSizeMode(\Endroid\QrCode\RoundBlockSizeMode::Margin)
    ->validateResult(false)
    ->build();

// Pastikan direktori ada
if (!file_exists(public_path('img/qrCode'))) {
    mkdir(public_path('img/qrCode'), 0755, true);
}

// Simpan QR code dasar
$qrPath = public_path('img/qrCode/' . $qrCode . '.png');
echo "Menyimpan QR code dasar ke $qrPath\n";
$result->saveToFile($qrPath);

// Verifikasi file QR code
if (file_exists($qrPath)) {
    echo "File QR code berhasil dibuat: $qrPath\n";
    echo "Ukuran file: " . filesize($qrPath) . " bytes\n";
} else {
    echo "File QR code gagal dibuat\n";
    exit(1);
}

// Langkah 2: Ambil QR dengan CustomQrController
echo "\nMengambil template default\n";
$template = App\Models\CustomQrTemplate::where('is_default', true)->first();
echo "Template: #{$template->id} ({$template->name})\n";

// Coba generate dengan CustomQrController
$controller = new App\Http\Controllers\CustomQrController();
$invitation = App\Models\Invitation::where('qrcode_invitation', $qrCode)->first();

echo "Menjalankan generateQrForGuest untuk invitation #{$invitation->id_invitation}\n";
$result = $controller->generateQrForGuest($invitation->id_invitation, $template->id);

// Langkah 3: Update database
echo "Memperbarui database\n";
$invitation->custom_qr_template_id = $template->id;
$invitation->image_qrcode_invitation = '/img/qrCode/' . $qrCode . '.png';
$invitation->save();

// Langkah 4: Verifikasi file
echo "Memverifikasi file akhir\n";
if (file_exists($qrPath)) {
    echo "File QR code ada di: $qrPath\n";
    echo "Ukuran file: " . filesize($qrPath) . " bytes\n";
    echo "Waktu modifikasi: " . date('Y-m-d H:i:s', filemtime($qrPath)) . "\n";
} else {
    echo "File QR code tidak ada: $qrPath\n";
}

echo "\nProses selesai.\n"; 