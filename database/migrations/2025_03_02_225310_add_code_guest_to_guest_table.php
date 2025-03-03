<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        Schema::table('guest', function (Blueprint $table) {
            $table->string('code_guest', 50)->after('nik_guest')->nullable();
        });

        // Generate codes for existing records
        foreach (\App\Models\Guest::all() as $guest) {
            $guest->update(['code_guest' => Str::uuid()]);
        }

        Schema::table('guest', function (Blueprint $table) {
            $table->string('code_guest')->nullable(false)->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('guest', function (Blueprint $table) {
            $table->dropUnique(['code_guest']);
            $table->dropColumn('code_guest');
        });
    }
};
