<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting column addition...\n";

// Add missing columns to invitation table
try {
    if (Schema::hasTable('invitation')) {
        echo "Invitation table exists\n";
        
        // Add name_guest column if it doesn't exist
        if (!Schema::hasColumn('invitation', 'name_guest')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN name_guest VARCHAR(255) NULL AFTER id_invitation');
            echo "Added name_guest column\n";
        } else {
            echo "name_guest column already exists\n";
        }
        
        // Add email_guest column if it doesn't exist
        if (!Schema::hasColumn('invitation', 'email_guest')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN email_guest VARCHAR(255) NULL AFTER name_guest');
            echo "Added email_guest column\n";
        } else {
            echo "email_guest column already exists\n";
        }
        
        // Add phone_guest column if it doesn't exist
        if (!Schema::hasColumn('invitation', 'phone_guest')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN phone_guest VARCHAR(255) NULL AFTER email_guest');
            echo "Added phone_guest column\n";
        } else {
            echo "phone_guest column already exists\n";
        }
        
        // Add address_guest column if it doesn't exist
        if (!Schema::hasColumn('invitation', 'address_guest')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN address_guest TEXT NULL AFTER phone_guest');
            echo "Added address_guest column\n";
        } else {
            echo "address_guest column already exists\n";
        }
        
        // Add company_guest column if it doesn't exist
        if (!Schema::hasColumn('invitation', 'company_guest')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN company_guest VARCHAR(255) NULL AFTER address_guest');
            echo "Added company_guest column\n";
        } else {
            echo "company_guest column already exists\n";
        }
        
        // Add created_by_guest column if it doesn't exist
        if (!Schema::hasColumn('invitation', 'created_by_guest')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN created_by_guest VARCHAR(255) NULL AFTER company_guest');
            echo "Added created_by_guest column\n";
        } else {
            echo "created_by_guest column already exists\n";
        }
        
        // Add custom_message column if it doesn't exist
        if (!Schema::hasColumn('invitation', 'custom_message')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN custom_message TEXT NULL AFTER created_by_guest');
            echo "Added custom_message column\n";
        } else {
            echo "custom_message column already exists\n";
        }
        
        // Add RSVP related columns
        if (!Schema::hasColumn('invitation', 'rsvp_status')) {
            DB::statement("ALTER TABLE invitation ADD COLUMN rsvp_status ENUM('pending', 'yes', 'no', 'maybe') NOT NULL DEFAULT 'pending' AFTER custom_message");
            echo "Added rsvp_status column\n";
        } else {
            echo "rsvp_status column already exists\n";
        }
        
        if (!Schema::hasColumn('invitation', 'plus_ones_count')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN plus_ones_count INT NOT NULL DEFAULT 0 AFTER rsvp_status');
            echo "Added plus_ones_count column\n";
        } else {
            echo "plus_ones_count column already exists\n";
        }
        
        if (!Schema::hasColumn('invitation', 'plus_ones_names')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN plus_ones_names VARCHAR(255) NULL AFTER plus_ones_count');
            echo "Added plus_ones_names column\n";
        } else {
            echo "plus_ones_names column already exists\n";
        }
        
        if (!Schema::hasColumn('invitation', 'dietary_preferences')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN dietary_preferences TEXT NULL AFTER plus_ones_names');
            echo "Added dietary_preferences column\n";
        } else {
            echo "dietary_preferences column already exists\n";
        }
        
        if (!Schema::hasColumn('invitation', 'rsvp_notes')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN rsvp_notes TEXT NULL AFTER dietary_preferences');
            echo "Added rsvp_notes column\n";
        } else {
            echo "rsvp_notes column already exists\n";
        }
        
        if (!Schema::hasColumn('invitation', 'rsvp_responded_at')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN rsvp_responded_at TIMESTAMP NULL AFTER rsvp_notes');
            echo "Added rsvp_responded_at column\n";
        } else {
            echo "rsvp_responded_at column already exists\n";
        }
        
        if (!Schema::hasColumn('invitation', 'rsvp_reminder_sent_at')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN rsvp_reminder_sent_at TIMESTAMP NULL AFTER rsvp_responded_at');
            echo "Added rsvp_reminder_sent_at column\n";
        } else {
            echo "rsvp_reminder_sent_at column already exists\n";
        }
        
        // Add souvenir related columns
        if (!Schema::hasColumn('invitation', 'souvenir_claimed')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN souvenir_claimed TINYINT(1) NOT NULL DEFAULT 0 AFTER checkout_img_invitation');
            echo "Added souvenir_claimed column\n";
        } else {
            echo "souvenir_claimed column already exists\n";
        }
        
        if (!Schema::hasColumn('invitation', 'souvenir_claimed_at')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN souvenir_claimed_at TIMESTAMP NULL AFTER souvenir_claimed');
            echo "Added souvenir_claimed_at column\n";
        } else {
            echo "souvenir_claimed_at column already exists\n";
        }
        
        if (!Schema::hasColumn('invitation', 'souvenir_claimed_img')) {
            DB::statement('ALTER TABLE invitation ADD COLUMN souvenir_claimed_img VARCHAR(100) NULL AFTER souvenir_claimed_at');
            echo "Added souvenir_claimed_img column\n";
        } else {
            echo "souvenir_claimed_img column already exists\n";
        }
    } else {
        echo "Invitation table does not exist\n";
    }
    
    // Ensure setting table has the enable_custom_qr column
    if (Schema::hasTable('setting')) {
        echo "Setting table exists\n";
        
        if (!Schema::hasColumn('setting', 'enable_custom_qr')) {
            DB::statement('ALTER TABLE setting ADD COLUMN enable_custom_qr TINYINT(1) NOT NULL DEFAULT 0');
            echo "Added enable_custom_qr column\n";
        } else {
            echo "enable_custom_qr column already exists\n";
        }
        
        if (!Schema::hasColumn('setting', 'enable_rsvp')) {
            DB::statement('ALTER TABLE setting ADD COLUMN enable_rsvp TINYINT(1) NOT NULL DEFAULT 0');
            echo "Added enable_rsvp column\n";
        } else {
            echo "enable_rsvp column already exists\n";
        }
    } else {
        echo "Setting table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Column addition complete\n"; 