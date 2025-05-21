<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\CustomQrTemplate;

try {
    // Check the table columns first
    $columns = DB::getSchemaBuilder()->getColumnListing('custom_qr_templates');
    echo "Available columns in custom_qr_templates table: " . implode(', ', $columns) . "\n";
    
    // Create a default template record
    $template = CustomQrTemplate::create([
        'name' => 'Default Template',
        'fg_color' => json_encode(['r' => 0, 'g' => 0, 'b' => 0]),
        'bg_color' => json_encode(['r' => 255, 'g' => 255, 'b' => 255]),
        'shape' => 'square',
        'error_correction' => 'H',
        'is_default' => true
    ]);
    
    echo "Created template with ID: " . $template->id . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 