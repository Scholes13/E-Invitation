<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        try {
            // Check if guest table exists
            if (Schema::hasTable('guest')) {
                // First check if the column exists
                if (!Schema::hasColumn('guest', 'code_guest')) {
                    Schema::table('guest', function (Blueprint $table) {
                        // Check if nik_guest column exists
                        if (Schema::hasColumn('guest', 'nik_guest')) {
                            $table->string('code_guest', 50)->after('nik_guest')->nullable();
                        } else {
                            $table->string('code_guest', 50)->nullable();
                        }
                    });
                    
                    Log::info('Added code_guest column to guest table');
                    
                    // Check if Guest model exists before using it
                    if (class_exists('\App\Models\Guest')) {
                        // Generate codes for existing records
                        foreach (\App\Models\Guest::all() as $guest) {
                            $guest->update(['code_guest' => Str::uuid()]);
                        }
                        
                        Schema::table('guest', function (Blueprint $table) {
                            $table->string('code_guest')->nullable(false)->unique()->change();
                        });
                        
                        Log::info('Generated unique codes for all guests and made code_guest column unique');
                    } else {
                        Log::warning('Guest model not found, skipping code generation');
                    }
                } else {
                    Log::info('code_guest column already exists in guest table');
                }
            } else {
                Log::info('Guest table does not exist, it may have been merged into invitation table');
            }
        } catch (\Exception $e) {
            Log::error('Error in add_code_guest_to_guest_table migration: ' . $e->getMessage());
            // Don't rethrow, let the migration continue
        }
    }

    public function down()
    {
        try {
            if (Schema::hasTable('guest') && Schema::hasColumn('guest', 'code_guest')) {
                Schema::table('guest', function (Blueprint $table) {
                    // Check if the index exists before trying to drop it
                    $sm = Schema::getConnection()->getDoctrineSchemaManager();
                    $indexes = $sm->listTableIndexes('guest');
                    
                    if (isset($indexes['guest_code_guest_unique'])) {
                        $table->dropUnique(['code_guest']);
                    }
                    
                    $table->dropColumn('code_guest');
                });
                
                Log::info('Dropped code_guest column from guest table');
            }
        } catch (\Exception $e) {
            Log::error('Error in add_code_guest_to_guest_table rollback: ' . $e->getMessage());
            // Don't rethrow, let the migration continue
        }
    }
};
