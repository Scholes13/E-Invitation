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
            if (!Schema::hasColumn('invitation', 'tracking_method')) {
                $table->string('tracking_method')->nullable()->after('tracking_client');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            if (Schema::hasColumn('invitation', 'tracking_method')) {
                $table->dropColumn('tracking_method');
            }
        });
    }
};
