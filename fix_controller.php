<?php

$file = 'app/Http/Controllers/CustomQrController.php';
$content = file_get_contents($file);

// Replace all instances of !isset(mySetting()->enable_custom_qr) with !property_exists(mySetting(), 'enable_custom_qr')
$newContent = str_replace(
    '!isset(mySetting()->enable_custom_qr)', 
    '!property_exists(mySetting(), \'enable_custom_qr\')', 
    $content
);

// Save the file
file_put_contents($file, $newContent);

echo "Updated " . substr_count($content, '!isset(mySetting()->enable_custom_qr)') . " instances\n"; 