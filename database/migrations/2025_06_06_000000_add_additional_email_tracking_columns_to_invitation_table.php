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
            // Email delivery tracking
            $table->boolean('email_delivered')->default(false)->after('email_sent');
            $table->timestamp('email_delivery_time')->nullable()->after('email_delivered');
            
            // Email complaint tracking (for spam reports)
            $table->boolean('email_complaint')->default(false)->after('email_bounced');
            $table->timestamp('email_complaint_time')->nullable()->after('email_complaint');
            
            // Email bounce tracking
            $table->timestamp('email_bounce_time')->nullable()->after('email_bounced');
            
            // General email status field
            $table->string('email_status')->nullable()->after('email_bounce_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            $table->dropColumn([
                'email_delivered',
                'email_delivery_time',
                'email_complaint',
                'email_complaint_time',
                'email_bounce_time',
                'email_status'
            ]);
        });
    }
}; 