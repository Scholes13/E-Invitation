<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Schema::table('invitation', function (Blueprint $table) {
                // Determine an existing column to place new fields after
                $existingColumns = ['custom_message', 'email_guest', 'checkout_invitation', 'created_at'];
                $afterColumn = null;
                
                foreach ($existingColumns as $column) {
                    if (Schema::hasColumn('invitation', $column)) {
                        $afterColumn = $column;
                        break;
                    }
                }
                
                // Add the rsvp_status column first, either after our found column or at the end
                if (!Schema::hasColumn('invitation', 'rsvp_status')) {
                    if ($afterColumn) {
                        $table->enum('rsvp_status', ['pending', 'yes', 'no', 'maybe'])->default('pending')->after($afterColumn);
                    } else {
                        $table->enum('rsvp_status', ['pending', 'yes', 'no', 'maybe'])->default('pending');
                    }
                    $afterColumn = 'rsvp_status';
                }
                
                // Add plus_ones_count after rsvp_status
                if (!Schema::hasColumn('invitation', 'plus_ones_count')) {
                    $table->integer('plus_ones_count')->default(0)->after($afterColumn);
                    $afterColumn = 'plus_ones_count';
                }
                
                // Add plus_ones_names after plus_ones_count
                if (!Schema::hasColumn('invitation', 'plus_ones_names')) {
                    $table->string('plus_ones_names')->nullable()->after($afterColumn);
                    $afterColumn = 'plus_ones_names';
                }
                
                // Add dietary_preferences after plus_ones_names
                if (!Schema::hasColumn('invitation', 'dietary_preferences')) {
                    $table->text('dietary_preferences')->nullable()->after($afterColumn);
                    $afterColumn = 'dietary_preferences';
                }
                
                // Add rsvp_notes after dietary_preferences
                if (!Schema::hasColumn('invitation', 'rsvp_notes')) {
                    $table->text('rsvp_notes')->nullable()->after($afterColumn);
                    $afterColumn = 'rsvp_notes';
                }
                
                // Add rsvp_responded_at after rsvp_notes
                if (!Schema::hasColumn('invitation', 'rsvp_responded_at')) {
                    $table->timestamp('rsvp_responded_at')->nullable()->after($afterColumn);
                    $afterColumn = 'rsvp_responded_at';
                }
                
                // Add rsvp_reminder_sent_at after rsvp_responded_at
                if (!Schema::hasColumn('invitation', 'rsvp_reminder_sent_at')) {
                    $table->timestamp('rsvp_reminder_sent_at')->nullable()->after($afterColumn);
                }
            });
        } catch (\Exception $e) {
            Log::error('RSVP fields migration error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            $columns = [
                'rsvp_status',
                'plus_ones_count',
                'plus_ones_names',
                'dietary_preferences',
                'rsvp_notes',
                'rsvp_responded_at',
                'rsvp_reminder_sent_at'
            ];
            
            // Only drop columns that exist
            $columnsToRemove = [];
            foreach ($columns as $column) {
                if (Schema::hasColumn('invitation', $column)) {
                    $columnsToRemove[] = $column;
                }
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
