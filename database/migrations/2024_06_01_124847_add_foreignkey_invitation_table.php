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
            $table->foreign('id_guest')->references('id_guest')->on('guest')
                ->onUpdate('cascade')    
                ->onDelete('cascade');

            $table->bigInteger('id_event')->unsigned()->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation', function (Blueprint $table) {
            $table->dropForeign('invitation_id_guest_foreign');

            $table->dropColumn('id_event');
        });
    }
};
