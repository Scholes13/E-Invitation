<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Create setting table if it doesn't exist
            if (!Schema::hasTable('setting')) {
                Schema::create('setting', function (Blueprint $table) {
                    $table->id();
                    $table->string('name_app');
                    $table->string('logo_app')->nullable();
                    $table->string('color_bg_app', 20)->nullable();
                    $table->string('image_bg_app')->nullable();
                    $table->boolean('image_bg_status')->default(TRUE);
                    $table->boolean('send_email')->nullable()->default(FALSE);
                    $table->boolean('send_whatsapp')->nullable()->default(FALSE);
                    $table->boolean('greeting_page')->nullable()->default(FALSE);
                    $table->longText('email_template_blasting')->nullable();
                    $table->timestamps();
                });
                
                // Insert default setting
                DB::table('setting')->insert([
                    'name_app' => 'QR Scan App',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                Log::info('Created setting table with default values');
            }
            
            Schema::table('setting', function (Blueprint $table) {
                if (!Schema::hasColumn('setting', 'enable_rsvp')) {
                    $table->boolean('enable_rsvp')->default(false)->after('greeting_page');
                }
                
                if (!Schema::hasColumn('setting', 'rsvp_deadline')) {
                    $table->date('rsvp_deadline')->nullable()->after('enable_rsvp');
                }
                
                if (!Schema::hasColumn('setting', 'enable_plus_ones')) {
                    $table->boolean('enable_plus_ones')->default(false)->after('rsvp_deadline');
                }
                
                if (!Schema::hasColumn('setting', 'collect_dietary_preferences')) {
                    $table->boolean('collect_dietary_preferences')->default(false)->after('enable_plus_ones');
                }
                
                if (!Schema::hasColumn('setting', 'send_rsvp_reminders')) {
                    $table->boolean('send_rsvp_reminders')->default(false)->after('collect_dietary_preferences');
                }
                
                if (!Schema::hasColumn('setting', 'reminder_days_before_deadline')) {
                    $table->integer('reminder_days_before_deadline')->default(3)->after('send_rsvp_reminders');
                }
                
                if (!Schema::hasColumn('setting', 'rsvp_email_template')) {
                    $table->text('rsvp_email_template')->nullable()->after('reminder_days_before_deadline');
                }
                
                if (!Schema::hasColumn('setting', 'rsvp_email_subject')) {
                    $table->string('rsvp_email_subject')->nullable()->after('rsvp_email_template');
                }
            });
        } catch (\Exception $e) {
            Log::error('RSVP settings migration error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            $columns = [
                'enable_rsvp',
                'rsvp_deadline',
                'enable_plus_ones',
                'collect_dietary_preferences',
                'send_rsvp_reminders',
                'reminder_days_before_deadline',
                'rsvp_email_template',
                'rsvp_email_subject'
            ];
            
            // Only drop columns that exist
            $columnsToRemove = [];
            foreach ($columns as $column) {
                if (Schema::hasColumn('setting', $column)) {
                    $columnsToRemove[] = $column;
                }
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
