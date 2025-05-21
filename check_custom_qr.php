<?php
// Load framework
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get default template
$template = App\Models\CustomQrTemplate::where('is_default', true)->first();
echo "Template default: ID #{$template->id} ({$template->name})\n";
echo "Logo path: {$template->logo_path}\n";

// Check if logo exists
if ($template->logo_path) {
    if (\Illuminate\Support\Facades\Storage::exists($template->logo_path)) {
        echo "Logo ada di storage\n";
        $logoFullPath = \Illuminate\Support\Facades\Storage::path($template->logo_path);
        echo "Logo full path: {$logoFullPath}\n";
        
        if (file_exists($logoFullPath)) {
            echo "File logo ada di: {$logoFullPath}\n";
            echo "Ukuran logo: " . filesize($logoFullPath) . " bytes\n";
        } else {
            echo "File logo tidak ada di filesystem\n";
        }
    } else {
        echo "Logo tidak ada di storage\n";
    }
}

// Generate test QR with logo
echo "\nMencoba generate QR code dengan logo dari CustomQrController\n";
$controller = new App\Http\Controllers\CustomQrController();
$qrCode = 'i7x1xl';

// Debug template
echo "Settings JSON: " . substr($template->settings_json, 0, 150) . "...\n";

// Dump template options
$settings = json_decode($template->settings_json, true);
echo "Image setting: " . ($settings['image'] ?? 'tidak ada') . "\n";

echo "Menjalankan generateQrForGuest\n";
$result = $controller->generateQrForGuest(8, $template->id);

// Convert result to string
if (is_object($result) && method_exists($result, 'getContent')) {
    $content = $result->getContent();
    echo "Result content: " . substr($content, 0, 150) . "...\n";
} else {
    echo "Result: " . json_encode($result) . "\n";
}

// Verify QR code file
$path = public_path('img/qrCode/' . $qrCode . '.png');
if (file_exists($path)) {
    echo "File QR code ada di: {$path}\n";
    echo "Ukuran file: " . filesize($path) . " bytes\n";
    echo "Waktu modifikasi: " . date('Y-m-d H:i:s', filemtime($path)) . "\n";
} else {
    echo "File QR code tidak ada di: {$path}\n";
} 