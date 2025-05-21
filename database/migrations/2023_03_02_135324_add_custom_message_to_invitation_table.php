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
            Schema::table('invitations', function (Blueprint $table) {
                if (!Schema::hasColumn('invitations', 'custom_message')) {
                    $table->text('custom_message')->nullable();
                } else {
                    Log::info('custom_message column already exists in invitations table');
                }
            });
        } catch (\Exception $e) {
            Log::error('Error adding custom_message to invitations table: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('invitations', function (Blueprint $table) {
                if (Schema::hasColumn('invitations', 'custom_message')) {
                    $table->dropColumn('custom_message');
                }
            });
        } catch (\Exception $e) {
            Log::error('Error removing custom_message from invitations table: ' . $e->getMessage());
            throw $e;
        }
    }
};
