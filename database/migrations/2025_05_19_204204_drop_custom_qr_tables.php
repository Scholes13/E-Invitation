<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First remove constraint from invitations table
        if (Schema::hasTable('invitations') && Schema::hasColumn('invitations', 'custom_qr_template_id')) {
            // Check for foreign key constraints
            $constraints = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE REFERENCED_TABLE_NAME = 'custom_qr_templates'
                AND TABLE_NAME = 'invitations'
                AND CONSTRAINT_SCHEMA = DATABASE()
            ");
            
            // Drop foreign key constraints first
            Schema::table('invitations', function (Blueprint $table) use ($constraints) {
                foreach ($constraints as $constraint) {
                    $table->dropForeign($constraint->CONSTRAINT_NAME);
                }
                
                // Drop the column
                $table->dropColumn('custom_qr_template_id');
            });
        }
        
        // Then drop the custom template table
        Schema::dropIfExists('custom_qr_templates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This cannot be undone automatically
        // To restore, you would need a separate migration to recreate the tables
    }
};
