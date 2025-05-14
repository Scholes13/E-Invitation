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
            $table->boolean('souvenir_claimed')->default(false)->after('checkout_img_invitation');
            $table->timestamp('souvenir_claimed_at')->nullable()->after('souvenir_claimed');
            $table->string('souvenir_claimed_img', 100)->nullable()->after('souvenir_claimed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            $table->dropColumn('souvenir_claimed');
            $table->dropColumn('souvenir_claimed_at');
            $table->dropColumn('souvenir_claimed_img');
        });
    }
};
