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
            $table->enum('rsvp_status', ['pending', 'yes', 'no', 'maybe'])->default('pending')->after('custom_message');
            $table->integer('plus_ones_count')->default(0)->after('rsvp_status');
            $table->string('plus_ones_names')->nullable()->after('plus_ones_count');
            $table->text('dietary_preferences')->nullable()->after('plus_ones_names');
            $table->text('rsvp_notes')->nullable()->after('dietary_preferences');
            $table->timestamp('rsvp_responded_at')->nullable()->after('rsvp_notes');
            $table->timestamp('rsvp_reminder_sent_at')->nullable()->after('rsvp_responded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            $table->dropColumn([
                'rsvp_status',
                'plus_ones_count',
                'plus_ones_names',
                'dietary_preferences',
                'rsvp_notes',
                'rsvp_responded_at',
                'rsvp_reminder_sent_at'
            ]);
        });
    }
};
