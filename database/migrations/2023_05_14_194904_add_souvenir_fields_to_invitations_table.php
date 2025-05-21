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
            if (Schema::hasTable('invitation')) {
                Schema::table('invitation', function (Blueprint $table) {
                    if (!Schema::hasColumn('invitation', 'souvenir_claimed')) {
                        $table->boolean('souvenir_claimed')->default(false)->after('checkout_img_invitation');
                    }
                    
                    if (!Schema::hasColumn('invitation', 'souvenir_claimed_at')) {
                        $table->timestamp('souvenir_claimed_at')->nullable()->after('souvenir_claimed');
                    }
                    
                    if (!Schema::hasColumn('invitation', 'souvenir_claimed_img')) {
                        $table->string('souvenir_claimed_img', 100)->nullable()->after('souvenir_claimed_at');
                    }
                });
                
                Log::info('Added souvenir fields to invitation table');
            } else {
                Log::warning('Invitation table does not exist, cannot add souvenir fields');
            }
        } catch (\Exception $e) {
            Log::error('Error adding souvenir fields: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            if (Schema::hasTable('invitation')) {
                Schema::table('invitation', function (Blueprint $table) {
                    // Only drop columns that exist
                    $columns = ['souvenir_claimed', 'souvenir_claimed_at', 'souvenir_claimed_img'];
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
                
                Log::info('Removed souvenir fields from invitation table');
            }
        } catch (\Exception $e) {
            Log::error('Error removing souvenir fields: ' . $e->getMessage());
            throw $e;
        }
    }
};
