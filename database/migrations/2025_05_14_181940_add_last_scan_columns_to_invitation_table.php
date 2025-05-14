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
            $table->timestamp('last_scan_in')->nullable()->after('checkout_img_invitation');
            $table->timestamp('last_scan_out')->nullable()->after('last_scan_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            $table->dropColumn('last_scan_in');
            $table->dropColumn('last_scan_out');
        });
    }
};
