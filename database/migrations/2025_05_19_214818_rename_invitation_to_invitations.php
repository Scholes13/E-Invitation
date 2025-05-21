<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Periksa apakah tabel invitation ada
        if (Schema::hasTable('invitation') && !Schema::hasTable('invitations')) {
            // Rename tabel dari invitation menjadi invitations
            Schema::rename('invitation', 'invitations');
        } else {
            // Jika tabel invitations sudah ada, skip
            if (Schema::hasTable('invitations')) {
                // Tabel sudah dalam format yang benar
            } else if (!Schema::hasTable('invitation')) {
                // Buat tabel baru sesuai dengan model jika tidak ada keduanya
                Schema::create('invitations', function (Blueprint $table) {
                    $table->bigIncrements('id_invitation');
                    $table->string("name_guest");
                    $table->string("email_guest");
                    $table->string("phone_guest");
                    $table->text("address_guest")->nullable();
                    $table->string("company_guest")->nullable();
                    $table->string("created_by_guest");
                    $table->text("custom_message")->nullable();
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
                    $table->timestamp('rsvp_responded_at')->nullable();
                    $table->boolean('email_sent')->default(false);
                    $table->boolean('email_read')->default(false);
                    $table->boolean('email_bounced')->default(false);
                    $table->string('tracking_code')->nullable();
                    $table->timestamp('last_tracked_at')->nullable();
                    $table->integer('tracking_count')->default(0);
                    $table->string('tracking_client')->nullable();
                    $table->string('tracking_method')->nullable();
                    $table->timestamp('last_scan_in')->nullable();
                    $table->timestamp('last_scan_out')->nullable();
                    $table->boolean('souvenir_claimed')->default(false);
                    $table->timestamp('souvenir_claimed_at')->nullable();
                    $table->string('souvenir_claimed_img')->nullable();
                    $table->enum('rsvp_status', ['pending', 'attending', 'not_attending'])->default('pending');
                    $table->integer('plus_ones_count')->default(0);
                    $table->text('plus_ones_names')->nullable();
                    $table->text('dietary_preferences')->nullable();
                    $table->text('rsvp_notes')->nullable();
                    $table->timestamp('rsvp_reminder_sent_at')->nullable();
                    $table->bigInteger('id_event')->unsigned()->default(1);
                    $table->timestamps();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invitations') && !Schema::hasTable('invitation')) {
            Schema::rename('invitations', 'invitation');
        }
    }
};
