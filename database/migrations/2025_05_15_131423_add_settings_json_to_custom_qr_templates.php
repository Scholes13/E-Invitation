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
            if (!Schema::hasColumn('custom_qr_templates', 'settings_json')) {
                $table->longText('settings_json')->nullable()->after('brand_theme');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_qr_templates', function (Blueprint $table) {
            if (Schema::hasColumn('custom_qr_templates', 'settings_json')) {
                $table->dropColumn('settings_json');
            }
        });
    }
};
