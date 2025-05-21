<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // First check if the table exists
        $tableExists = Schema::hasTable('custom_qr_templates');
        if ($tableExists) {
            // Drop the custom QR templates table
            DB::statement('DROP TABLE IF EXISTS custom_qr_templates;');
        }
        
        // If invitations table exists, drop custom_qr_template_id column
        if (Schema::hasTable('invitations') && Schema::hasColumn('invitations', 'custom_qr_template_id')) {
            Schema::table('invitations', function (Blueprint $table) {
                $table->dropColumn('custom_qr_template_id');
            });
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This cannot be undone
    }
};
