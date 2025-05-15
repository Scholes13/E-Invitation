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
            $table->text('secondary_color')->nullable()->after('bg_color');
            $table->boolean('is_branded')->default(false)->after('error_correction');
            $table->string('finder_pattern_style')->default('default')->after('shape');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_qr_templates', function (Blueprint $table) {
            $table->dropColumn([
                'secondary_color',
                'is_branded',
                'finder_pattern_style'
            ]);
        });
    }
};
