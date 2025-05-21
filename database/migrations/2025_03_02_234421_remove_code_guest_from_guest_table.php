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
        if (Schema::hasTable('guest') && Schema::hasColumn('guest', 'code_guest')) {
        Schema::table('guest', function (Blueprint $table) {
            $table->dropColumn('code_guest');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('guest') && !Schema::hasColumn('guest', 'code_guest')) {
            Schema::table('guest', function (Blueprint $table) {
            $table->string('code_guest')->after('id');
        });
        }
    }
};
