<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Invitation;

class MigrateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:migrate {--force : Force migration without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate QR codes from old locations to standardized storage location';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting QR code migration...');
        
        // Check if storage directory exists, if not create it
        if (!Storage::exists('public/img/qrCode')) {
            Storage::makeDirectory('public/img/qrCode');
            $this->info('Created directory: public/img/qrCode');
        }
        
        // Get all existing QR codes from public directory
        $publicPath = public_path('img/qrCode');
        if (!File::exists($publicPath)) {
            $this->warn('Public QR code directory not found: ' . $publicPath);
        } else {
            $files = File::files($publicPath);
            $this->info('Found ' . count($files) . ' QR code files in public directory');
            
            if (count($files) > 0 && !$this->option('force')) {
                if (!$this->confirm('Do you want to migrate these files to storage?')) {
                    $this->info('Migration canceled.');
                    return;
                }
            }
            
            $migrated = 0;
            $errors = 0;
            
            foreach ($files as $file) {
                $filename = $file->getFilename();
                $qrcode = pathinfo($filename, PATHINFO_FILENAME);
                
                try {
                    // Copy file to storage
                    $storagePath = 'public/img/qrCode/' . $filename;
                    
                    if (Storage::exists($storagePath)) {
                        // File already exists in storage
                        $this->line("File already exists in storage: {$filename}");
                    } else {
                        Storage::put($storagePath, File::get($file->getPathname()));
                        $this->line("Migrated file: {$filename}");
                        $migrated++;
                    }
                    
                    // Update invitation record
                    $invitation = Invitation::where('qrcode_invitation', $qrcode)->first();
                    if ($invitation) {
                        if (empty($invitation->custom_qr_path)) {
                            $invitation->update(['custom_qr_path' => $storagePath]);
                            $this->line("Updated invitation record for: {$qrcode}");
                        }
                    } else {
                        $this->warn("No invitation found for QR code: {$qrcode}");
                    }
                } catch (\Exception $e) {
                    $this->error("Error migrating {$filename}: " . $e->getMessage());
                    $errors++;
                }
            }
            
            $this->info("Migration completed: {$migrated} files migrated, {$errors} errors");
        }
        
        // Remind to create symlink
        if (!file_exists(public_path('storage'))) {
            $this->warn('Public storage symlink not found!');
            $this->info('Run the following command to create it:');
            $this->line('php artisan storage:link');
        }
    }
} 