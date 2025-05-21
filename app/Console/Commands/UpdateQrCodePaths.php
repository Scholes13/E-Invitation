<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invitation;
use Illuminate\Support\Facades\DB;

class UpdateQrCodePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:update-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update image_qrcode_invitation paths for all invitations for consistency';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting QR code path update...');

        try {
            // Find invitations with custom_qr_path but inconsistent image_qrcode_invitation
            $invitationsToUpdate = Invitation::whereNotNull('custom_qr_path')
                ->get();
                
            $count = 0;
            
            foreach ($invitationsToUpdate as $invitation) {
                $qrcode = $invitation->qrcode_invitation;
                $newPath = '/img/qrCode/' . $qrcode . '.png';
                
                // Update to consistent path
                $invitation->update([
                    'image_qrcode_invitation' => $newPath
                ]);
                
                $count++;
                $this->line("Updated invitation #{$invitation->id_invitation} for QR code: {$qrcode}");
            }
            
            $this->info("Successfully updated {$count} invitation records");
            
            // Drop the custom_qr_path column if the user confirms
            if ($count > 0 && $this->confirm('Do you want to remove the unused custom_qr_path column from the database?')) {
                if (DB::connection()->getSchemaBuilder()->hasColumn('invitation', 'custom_qr_path')) {
                    DB::connection()->getSchemaBuilder()->table('invitation', function ($table) {
                        $table->dropColumn('custom_qr_path');
                    });
                    $this->info('Removed custom_qr_path column from invitation table');
                } else {
                    $this->info('Column custom_qr_path does not exist');
                }
            }
            
        } catch (\Exception $e) {
            $this->error('Error updating QR code paths: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 