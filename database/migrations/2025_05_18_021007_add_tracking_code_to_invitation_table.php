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
            $table->text('tracking_code')->nullable()->after('email_bounced');
            $table->timestamp('last_tracked_at')->nullable()->after('tracking_code');
            $table->integer('tracking_count')->default(0)->after('last_tracked_at');
            $table->string('tracking_client')->nullable()->after('tracking_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            $table->dropColumn('tracking_code');
            $table->dropColumn('last_tracked_at');
            $table->dropColumn('tracking_count');
            $table->dropColumn('tracking_client');
        });
    }
};
