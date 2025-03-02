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
        Schema::create('invitation', function (Blueprint $table) {
            $table->bigIncrements('id_invitation');
            $table->bigInteger('id_guest')->unsigned()->index();
            $table->string("qrcode_invitation", 20)->unique();
            $table->string("table_number_invitation", 20)->nullable();
            $table->enum('type_invitation', ['reguler', 'vip']);
            $table->text("information_invitation")->nullable();
            $table->text("link_invitation")->nullable();
            $table->text("image_qrcode_invitation")->nullable();
            $table->boolean('send_email_invitation')->default(FALSE);
            $table->string("checkin_img_invitation")->nullable();
            $table->string("checkout_img_invitation")->nullable();
            $table->timestamp('checkin_invitation')->nullable();
            $table->timestamp('checkout_invitation')->nullable();
            $table->bigInteger('id_user')->unsigned()->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitation');
    }
};
