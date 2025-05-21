<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // First, ensure the invitation table exists
            if (!Schema::hasTable('invitation')) {
                Schema::create('invitation', function (Blueprint $table) {
                    $table->bigIncrements('id_invitation');
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
                
                Log::info('Created invitation table');
            }
            
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
                if (!Schema::hasColumn('invitation', 'custom_message')) {
                    $table->text('custom_message')->nullable()->after('created_by_guest');
                }
            });

            // Add id_guest foreign key if not present already
            if (!Schema::hasColumn('invitation', 'id_guest')) {
                Schema::table('invitation', function (Blueprint $table) {
                    $table->bigInteger('id_guest')->unsigned()->nullable()->after('id_invitation');
                });
            }
            
            // Step 2: Copy data from Guest to Invitation if Guest table exists
            if (Schema::hasTable('guest')) {
                $guests = DB::table('guest')->get();
                
                foreach ($guests as $guest) {
                    // Find related invitation 
                    // Check if id_guest column exists, if not skip this part
                    if (Schema::hasColumn('invitation', 'id_guest')) {
                        $invitation = DB::table('invitation')->where('id_guest', $guest->id_guest)->first();
                        
                        if ($invitation) {
                            // Update invitation with guest data
                            $updateData = [];
                            
                            // Only include fields that exist in the invitation table
                            if (Schema::hasColumn('invitation', 'name_guest')) {
                                $updateData['name_guest'] = $guest->name_guest;
                            }
                            if (Schema::hasColumn('invitation', 'email_guest')) {
                                $updateData['email_guest'] = $guest->email_guest;
                            }
                            if (Schema::hasColumn('invitation', 'phone_guest')) {
                                $updateData['phone_guest'] = $guest->phone_guest;
                            }
                            if (Schema::hasColumn('invitation', 'address_guest')) {
                                $updateData['address_guest'] = $guest->address_guest;
                            }
                            if (Schema::hasColumn('invitation', 'company_guest')) {
                                $updateData['company_guest'] = $guest->company_guest;
                            }
                            if (Schema::hasColumn('invitation', 'created_by_guest')) {
                                $updateData['created_by_guest'] = $guest->created_by_guest;
                            }
                            if (Schema::hasColumn('invitation', 'custom_message')) {
                                $updateData['custom_message'] = $guest->custom_message;
                            }
                            
                            if (!empty($updateData)) {
                                DB::table('invitation')
                                    ->where('id_invitation', $invitation->id_invitation)
                                    ->update($updateData);
                            }
                        }
                    }
                }

                // Step 3: Drop foreign key constraint and remove id_guest column if it exists
                if (Schema::hasColumn('invitation', 'id_guest')) {
                    Schema::table('invitation', function (Blueprint $table) {
                        // Drop foreign key if it exists (depending on your setup)
                        try {
                            $table->dropForeign(['id_guest']);
                        } catch (\Exception $e) {
                            Log::warning('No foreign key constraint for id_guest: ' . $e->getMessage());
                        }
                    });
                }

                // Step 4: Drop the Guest table if it exists and is no longer needed
                Schema::dropIfExists('guest');
            }
        } catch (\Exception $e) {
            Log::error('Migration error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            // Only execute if the columns exist in invitation table
            if (Schema::hasColumn('invitation', 'name_guest')) {
                // Recreate Guest table
                if (!Schema::hasTable('guest')) {
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
                }

                // Move data from Invitation back to Guest
                $invitations = DB::table('invitation')->get();
                foreach ($invitations as $invitation) {
                    if (isset($invitation->name_guest) && $invitation->name_guest) {
                        // Prepare data for guest table
                        $guestData = [
                            'name_guest' => $invitation->name_guest,
                        ];
                        
                        // Only include fields if they exist in the invitation table
                        if (property_exists($invitation, 'email_guest')) {
                            $guestData['email_guest'] = $invitation->email_guest;
                        }
                        if (property_exists($invitation, 'phone_guest')) {
                            $guestData['phone_guest'] = $invitation->phone_guest;
                        }
                        if (property_exists($invitation, 'address_guest')) {
                            $guestData['address_guest'] = $invitation->address_guest;
                        }
                        if (property_exists($invitation, 'company_guest')) {
                            $guestData['company_guest'] = $invitation->company_guest;
                        }
                        if (property_exists($invitation, 'created_by_guest')) {
                            $guestData['created_by_guest'] = $invitation->created_by_guest;
                        }
                        if (property_exists($invitation, 'custom_message')) {
                            $guestData['custom_message'] = $invitation->custom_message;
                        }
                        if (property_exists($invitation, 'created_at')) {
                            $guestData['created_at'] = $invitation->created_at;
                        }
                        if (property_exists($invitation, 'updated_at')) {
                            $guestData['updated_at'] = $invitation->updated_at;
                        }

                        // Insert into guest table
                        $guestId = DB::table('guest')->insertGetId($guestData);

                        // Add id_guest column to invitation table if it doesn't exist
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

                // Get list of columns that should be removed and actually exist
                $columnsToRemove = [];
                $columnsToDrop = [
                    'name_guest',
                    'email_guest',
                    'phone_guest',
                    'address_guest',
                    'company_guest',
                    'created_by_guest',
                    'custom_message'
                ];

                foreach ($columnsToDrop as $column) {
                    if (Schema::hasColumn('invitation', $column)) {
                        $columnsToRemove[] = $column;
                    }
                }

                // Remove guest fields from invitation if any exist
                if (!empty($columnsToRemove)) {
                    Schema::table('invitation', function (Blueprint $table) use ($columnsToRemove) {
                        $table->dropColumn($columnsToRemove);
                    });
                }

                // Add foreign key constraint if both tables exist and columns exist
                if (Schema::hasTable('guest') && Schema::hasTable('invitation') && 
                    Schema::hasColumn('invitation', 'id_guest') && Schema::hasColumn('guest', 'id_guest')) {
                    Schema::table('invitation', function (Blueprint $table) {
                        $table->foreign('id_guest')->references('id_guest')->on('guest');
                    });
                }
            }
        } catch (\Exception $e) {
            Log::error('Migration rollback error: ' . $e->getMessage());
            throw $e;
        }
    }
};
