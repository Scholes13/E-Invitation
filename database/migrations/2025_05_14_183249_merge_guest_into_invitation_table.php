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
        // Step 1: Add Guest fields to the Invitation table
        Schema::table('invitation', function (Blueprint $table) {
            if (!Schema::hasColumn('invitation', 'name_guest')) {
                $table->string('name_guest')->nullable()->after('id_invitation');
            }
            if (!Schema::hasColumn('invitation', 'email_guest')) {
                $table->string('email_guest')->nullable()->after('name_guest');
            }
            if (!Schema::hasColumn('invitation', 'phone_guest')) {
                $table->string('phone_guest')->nullable()->after('email_guest');
            }
            if (!Schema::hasColumn('invitation', 'address_guest')) {
                $table->text('address_guest')->nullable()->after('phone_guest');
            }
            if (!Schema::hasColumn('invitation', 'company_guest')) {
                $table->string('company_guest')->nullable()->after('address_guest');
            }
            if (!Schema::hasColumn('invitation', 'created_by_guest')) {
                $table->string('created_by_guest')->nullable()->after('company_guest');
            }
            // Skip custom_message as it already exists in the invitation table
        });

        // Step 2: Copy data from Guest to Invitation
        $guests = DB::table('guest')->get();
        foreach ($guests as $guest) {
            // Find related invitation
            $invitation = DB::table('invitation')->where('id_guest', $guest->id_guest)->first();
            
            if ($invitation) {
                // Update invitation with guest data
                DB::table('invitation')
                    ->where('id_invitation', $invitation->id_invitation)
                    ->update([
                        'name_guest' => $guest->name_guest,
                        'email_guest' => $guest->email_guest,
                        'phone_guest' => $guest->phone_guest,
                        'address_guest' => $guest->address_guest,
                        'company_guest' => $guest->company_guest,
                        'created_by_guest' => $guest->created_by_guest,
                        'custom_message' => $guest->custom_message
                    ]);
            }
        }

        // Step 3: Drop foreign key constraint and remove id_guest column
        Schema::table('invitation', function (Blueprint $table) {
            // Drop foreign key if it exists (depending on your setup)
            if (Schema::hasColumn('invitation', 'id_guest')) {
                $table->dropForeign(['id_guest']);
                $table->dropColumn('id_guest');
            }
        });

        // Step 4: Drop the Guest table
        Schema::dropIfExists('guest');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate Guest table
        Schema::create('guest', function (Blueprint $table) {
            $table->bigIncrements('id_guest');
            $table->string('name_guest');
            $table->string('email_guest')->nullable();
            $table->string('phone_guest')->nullable();
            $table->text('address_guest')->nullable();
            $table->string('company_guest')->nullable();
            $table->string('created_by_guest')->nullable();
            $table->text('custom_message')->nullable();
            $table->timestamps();
        });

        // Move data from Invitation back to Guest
        $invitations = DB::table('invitation')->get();
        foreach ($invitations as $invitation) {
            if ($invitation->name_guest) {
                // Insert into guest table
                $guestId = DB::table('guest')->insertGetId([
                    'name_guest' => $invitation->name_guest,
                    'email_guest' => $invitation->email_guest,
                    'phone_guest' => $invitation->phone_guest,
                    'address_guest' => $invitation->address_guest,
                    'company_guest' => $invitation->company_guest,
                    'created_by_guest' => $invitation->created_by_guest,
                    'custom_message' => $invitation->custom_message,
                    'created_at' => $invitation->created_at,
                    'updated_at' => $invitation->updated_at
                ]);

                // Add id_guest column to invitation table
                if (!Schema::hasColumn('invitation', 'id_guest')) {
                    Schema::table('invitation', function (Blueprint $table) {
                        $table->bigInteger('id_guest')->unsigned()->nullable()->after('id_invitation');
                    });
                }

                // Update invitation with guest_id
                DB::table('invitation')
                    ->where('id_invitation', $invitation->id_invitation)
                    ->update(['id_guest' => $guestId]);
            }
        }

        // Remove guest fields from invitation
        Schema::table('invitation', function (Blueprint $table) {
            $table->dropColumn([
                'name_guest',
                'email_guest',
                'phone_guest',
                'address_guest',
                'company_guest',
                'created_by_guest',
                'custom_message'
            ]);
        });

        // Add foreign key constraint
        Schema::table('invitation', function (Blueprint $table) {
            $table->foreign('id_guest')->references('id_guest')->on('guest');
        });
    }
};
