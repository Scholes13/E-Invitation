<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            if (!Schema::hasTable('invitations')) {
                Schema::create('invitations', function (Blueprint $table) {
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
                
                Log::info('Created invitations table');
            } else {
                Log::info('Invitations table already exists, skipping creation');
            }
        } catch (\Exception $e) {
            Log::error('Error creating invitations table: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
