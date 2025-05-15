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
        Schema::table('setting', function (Blueprint $table) {
            $table->boolean('enable_rsvp')->default(false)->after('greeting_page');
            $table->date('rsvp_deadline')->nullable()->after('enable_rsvp');
            $table->boolean('enable_plus_ones')->default(false)->after('rsvp_deadline');
            $table->boolean('collect_dietary_preferences')->default(false)->after('enable_plus_ones');
            $table->boolean('send_rsvp_reminders')->default(false)->after('collect_dietary_preferences');
            $table->integer('reminder_days_before_deadline')->default(3)->after('send_rsvp_reminders');
            $table->text('rsvp_email_template')->nullable()->after('reminder_days_before_deadline');
            $table->string('rsvp_email_subject')->nullable()->after('rsvp_email_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            $table->dropColumn([
                'enable_rsvp',
                'rsvp_deadline',
                'enable_plus_ones',
                'collect_dietary_preferences',
                'send_rsvp_reminders',
                'reminder_days_before_deadline',
                'rsvp_email_template',
                'rsvp_email_subject'
            ]);
        });
    }
};
