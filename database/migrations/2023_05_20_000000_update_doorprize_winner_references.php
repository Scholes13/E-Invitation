<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename id_guest to guest_id if necessary
        if (Schema::hasColumn('doorprizewinners', 'id_guest') &&
            !Schema::hasColumn('doorprizewinners', 'guest_id')) {
            Schema::table('doorprizewinners', function (Blueprint $table) {
                $table->renameColumn('id_guest', 'guest_id');
            });
        }

        // Add name and email columns if they don't exist
        if (!Schema::hasColumn('doorprizewinners', 'name')) {
            Schema::table('doorprizewinners', function (Blueprint $table) {
                $table->string('name')->nullable();
                $table->string('email')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('doorprizewinners', 'guest_id') &&
            !Schema::hasColumn('doorprizewinners', 'id_guest')) {
            Schema::table('doorprizewinners', function (Blueprint $table) {
                $table->renameColumn('guest_id', 'id_guest');
            });
        }

        if (Schema::hasColumn('doorprizewinners', 'name')) {
            Schema::table('doorprizewinners', function (Blueprint $table) {
                $table->dropColumn(['name', 'email']);
            });
        }
    }
}; 