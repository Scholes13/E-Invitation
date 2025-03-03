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
        Schema::create('setting', function (Blueprint $table) {
            $table->id();
            $table->string('name_app');
            $table->string('logo_app')->nullable();
            $table->string('color_bg_app', 20)->nullable();
            $table->string('image_bg_app')->nullable();
            $table->boolean('image_bg_status')->default(TRUE);
            $table->boolean('send_email')->nullable()->default(FALSE);
            $table->boolean('send_whatsapp')->nullable()->default(FALSE);
            $table->boolean('greeting_page')->nullable()->default(FALSE);
            $table->longText('email_template_blasting')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting');
    }
};
