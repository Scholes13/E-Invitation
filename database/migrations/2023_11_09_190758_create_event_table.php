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
        Schema::create('event', function (Blueprint $table) {
            $table->bigIncrements('id_event');
            $table->string('code_event', 20)->nullable();
            $table->text('name_event');
            $table->text('type_event');
            $table->text('place_event');
            $table->text('location_event');
            $table->timestamp("start_event")->nullable();
            $table->timestamp("end_event")->nullable();
            $table->text("information_event")->nullable();
            $table->string("image_event")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
