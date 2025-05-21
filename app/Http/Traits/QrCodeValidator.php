<?php

namespace App\Http\Traits;

trait QrCodeValidator
{
    /**
     * Helper function to standardize QR code format validation
     * 
     * @param string $qrcode The QR code to validate and sanitize
     * @return string The sanitized QR code or null if invalid
     */
    protected function validateQrCode($qrcode)
    {
        // Hapus whitespace dan karakter khusus
        $qrcode = trim($qrcode);
        
        // Remove any URL parts if present (for backward compatibility)
        if (strpos($qrcode, '/scan/verify/') !== false) {
            $qrcode = substr($qrcode, strrpos($qrcode, '/') + 1);
        }
        
        // Pastikan QR code tidak kosong
        if (empty($qrcode)) {
            return null;
        }
        
        // Validasi panjang QR code (sesuaikan dengan standar aplikasi)
        if (strlen($qrcode) < 5 || strlen($qrcode) > 100) {
            return null;
        }
        
        return $qrcode;
    }
} 