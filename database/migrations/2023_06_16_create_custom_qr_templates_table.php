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
        Schema::create('custom_qr_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('fg_color');
            $table->text('bg_color');
            $table->string('logo_path')->nullable();
            $table->enum('shape', ['square', 'round', 'dot'])->default('square');
            $table->enum('error_correction', ['L', 'M', 'Q', 'H'])->default('M');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_qr_templates');
    }
}; 