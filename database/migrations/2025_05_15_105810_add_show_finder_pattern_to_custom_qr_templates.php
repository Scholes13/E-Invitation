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
            $table->boolean('show_finder_pattern')->default(true)->after('finder_pattern_style');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_qr_templates', function (Blueprint $table) {
            $table->dropColumn('show_finder_pattern');
        });
    }
};
