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
        Schema::table('guest', function (Blueprint $table) {
            $table->string('nik_guest', 60)->nullable()->index();
            $table->enum('created_by_guest', ['admin', 'register'])->default("admin");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest', function($table) {
            $table->dropColumn('nik_guest');
            $table->dropColumn('created_by_guest');
        });
    }
};
