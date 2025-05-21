<?php
// Load framework
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

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

// Ambil semua invitation
$invitations = App\Models\Invitation::all();
$count = 0;
$errors = 0;

foreach ($invitations as $invitation) {
    $qrCode = $invitation->qrcode_invitation;
    echo "\nMemperbaiki QR code: $qrCode untuk {$invitation->name_guest}\n";
    
    try {
        // Buat QR code base
        $qrCodeObj = \Endroid\QrCode\QrCode::create($qrCode)
            ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10);

        // Atur warna foreground (hitam)
        $qrCodeObj->setForegroundColor(new \Endroid\QrCode\Color\Color(0, 0, 0));

        // Atur warna background (putih)
        $qrCodeObj->setBackgroundColor(new \Endroid\QrCode\Color\Color(255, 255, 255));

        // Buat writer
        $writer = new \Endroid\QrCode\Writer\PngWriter();

        // Tambahkan logo
        $logo = \Endroid\QrCode\Logo\Logo::create($logoPath)
            ->setResizeToWidth(80); // Ukuran logo

        $result = $writer->write($qrCodeObj, $logo);

        // Simpan QR code
        $qrPath = public_path('img/qrCode/' . $qrCode . '.png');
        file_put_contents($qrPath, $result->getString());

        // Update invitation
        $invitation->custom_qr_template_id = $template->id;
        $invitation->image_qrcode_invitation = '/img/qrCode/' . $qrCode . '.png';
        $invitation->save();

        // Verifikasi file QR code
        if (file_exists($qrPath)) {
            echo "  - QR code berhasil dibuat: $qrPath\n";
            echo "  - Ukuran file: " . filesize($qrPath) . " bytes\n";
            $count++;
        } else {
            echo "  - QR code gagal dibuat\n";
            $errors++;
        }
    } catch (Exception $e) {
        echo "  - Error: " . $e->getMessage() . "\n";
        $errors++;
    }
}

echo "\nProses selesai. $count QR code berhasil diperbaiki. $errors error.\n"; 