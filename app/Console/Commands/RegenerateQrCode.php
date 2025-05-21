<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\InvitationController;
use App\Models\Invitation;

class RegenerateQrCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:regenerate {code? : QR code to regenerate, or "all" to regenerate all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate QR code with current template design';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $code = $this->argument('code');
        $controller = new InvitationController();
        
        // If no code provided, prompt for it
        if (!$code) {
            $code = $this->ask('Enter QR code to regenerate, or "all" to regenerate all QR codes');
        }
        
        // Handle "all" option
        if ($code === 'all') {
            $this->info('Regenerating all QR codes...');
            $invitations = Invitation::all();
            $total = $invitations->count();
            $bar = $this->output->createProgressBar($total);
            $bar->start();
            
            foreach ($invitations as $invitation) {
                $qrcode = $invitation->qrcode_invitation;
                if ($qrcode) {
                    try {
                        $controller->qrcodeGenerator($qrcode, true);
                    } catch (\Exception $e) {
                        $this->error("Error regenerating QR code {$qrcode}: " . $e->getMessage());
                    }
                }
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info('All QR codes regenerated successfully.');
            return;
        }
        
        // Check if the QR code exists
        $invitation = Invitation::where('qrcode_invitation', $code)->first();
        if (!$invitation) {
            $this->error("QR code '{$code}' not found.");
            return 1;
        }
        
        // Regenerate the specified QR code
        try {
            $this->info("Regenerating QR code: {$code}");
            $controller->qrcodeGenerator($code, true);
            $this->info("QR code regenerated successfully.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error regenerating QR code: " . $e->getMessage());
            return 1;
        }
    }
}
