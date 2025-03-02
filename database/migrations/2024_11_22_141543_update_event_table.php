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
        Schema::table('event', function (Blueprint $table) {
            $table->boolean('image_top_status')->default(TRUE);
            $table->string('color_text_event', 20)->nullable();
            $table->string('color_bg_event', 20)->nullable();
            $table->string('image_bg_event')->nullable();
            $table->boolean('image_bg_status')->default(TRUE);
            $table->string('image_left_event')->nullable();
            $table->boolean('image_left_status')->default(FALSE);
            $table->string('image_right_event')->nullable();
            $table->boolean('image_right_status')->default(FALSE);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event', function($table) {
            $table->dropColumn('image_top_status');
            $table->dropColumn('color_text_event');
            $table->dropColumn('color_bg_event');
            $table->dropColumn('image_bg_event');
            $table->dropColumn('image_bg_status');
            $table->dropColumn('image_left_event');
            $table->dropColumn('image_left_status');
            $table->dropColumn('image_right_event');
            $table->dropColumn('image_right_status');
        });
    }
};
