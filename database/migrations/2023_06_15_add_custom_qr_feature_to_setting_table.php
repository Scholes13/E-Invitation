<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Ensure the setting table exists
            if (!Schema::hasTable('setting')) {
                Log::warning('Setting table does not exist - cannot add custom_qr feature');
                return;
            }
            
            Schema::table('setting', function (Blueprint $table) {
                // Check if the column doesn't already exist
                if (!Schema::hasColumn('setting', 'enable_custom_qr')) {
                    $table->boolean('enable_custom_qr')->default(0);
                }
            });
        } catch (\Exception $e) {
            Log::error('Custom QR feature migration error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('setting', function (Blueprint $table) {
                if (Schema::hasColumn('setting', 'enable_custom_qr')) {
                    $table->dropColumn('enable_custom_qr');
                }
            });
        } catch (\Exception $e) {
            Log::error('Custom QR feature migration rollback error: ' . $e->getMessage());
            throw $e;
        }
    }
}; 