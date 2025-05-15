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
        Schema::table('custom_qr_templates', function (Blueprint $table) {
            $table->boolean('is_advanced_branded')->default(false)->after('is_branded');
            $table->string('brand_theme')->nullable()->after('is_advanced_branded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_qr_templates', function (Blueprint $table) {
            $table->dropColumn(['is_advanced_branded', 'brand_theme']);
        });
    }
}; 