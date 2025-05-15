<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            if (!Schema::hasColumn('invitation', 'custom_qr_path')) {
                $table->string('custom_qr_path')->nullable()->after('image_qrcode_invitation');
            }
            
            if (!Schema::hasColumn('invitation', 'custom_qr_template_id')) {
                $table->foreignId('custom_qr_template_id')->nullable()->after('custom_qr_path')
                      ->constrained('custom_qr_templates')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            if (Schema::hasColumn('invitation', 'custom_qr_template_id')) {
                $table->dropForeign(['custom_qr_template_id']);
            }
            
            if (Schema::hasColumn('invitation', 'custom_qr_path')) {
                $table->dropColumn('custom_qr_path');
            }
            
            if (Schema::hasColumn('invitation', 'custom_qr_template_id')) {
                $table->dropColumn('custom_qr_template_id');
            }
        });
    }
};
