<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Logo\Logo;
use App\Models\Invitation;
use App\Models\CustomQrTemplate;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CustomQrController extends Controller
{
    public function index()
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $templates = CustomQrTemplate::all();
        return view('custom-qr.index', compact('templates'));
    }
    
    public function create()
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        return view('custom-qr.create');
    }
    
    public function store(Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fg_color' => 'required|string',
            'bg_color' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'logo_size' => 'nullable|integer|min:30|max:150',
            'shape' => 'required|in:square,round,dot',
            'error_correction' => 'required|in:L,M,Q,H',
        ]);
        
        // Process the logo if uploaded
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('public/custom-qr/logos');
        }
        
        // Convert hex colors to RGB
        list($r, $g, $b) = sscanf($validated['fg_color'], "#%02x%02x%02x");
        $fgColor = ['r' => $r, 'g' => $g, 'b' => $b];
        
        list($r, $g, $b) = sscanf($validated['bg_color'], "#%02x%02x%02x");
        $bgColor = ['r' => $r, 'g' => $g, 'b' => $b];
        
        // Create template
        CustomQrTemplate::create([
            'name' => $validated['name'],
            'fg_color' => json_encode($fgColor),
            'bg_color' => json_encode($bgColor),
            'logo_path' => $logoPath,
            'logo_size' => $validated['logo_size'] ?? 60,
            'shape' => $validated['shape'],
            'error_correction' => $validated['error_correction'],
        ]);
        
        return redirect()->route('custom-qr.index')->with('success', 'Custom QR template created successfully');
    }
    
    public function edit($id)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $template = CustomQrTemplate::findOrFail($id);
        
        // Check if template is a branded QR code
        if ($template->is_branded) {
            // If it's an advanced branded QR, redirect to advanced editor
            if ($template->is_advanced_branded) {
                return redirect()->route('custom-qr.editAdvancedBranded', $id);
            }
            
            // Otherwise use the regular branded editor
            return view('custom-qr.edit-branded', compact('template'));
        }
        
        // Standard QR code editor
        return view('custom-qr.edit', compact('template'));
    }
    
    public function update(Request $request, $id)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $template = CustomQrTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fg_color' => 'required|string',
            'bg_color' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'logo_size' => 'nullable|integer|min:30|max:150',
            'shape' => 'required|in:square,round,dot',
            'error_correction' => 'required|in:L,M,Q,H',
        ]);
        
        // Process the logo if uploaded
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($template->logo_path) {
                Storage::delete($template->logo_path);
            }
            $logoPath = $request->file('logo')->store('public/custom-qr/logos');
            $template->logo_path = $logoPath;
        }
        
        // Convert hex colors to RGB
        list($r, $g, $b) = sscanf($validated['fg_color'], "#%02x%02x%02x");
        $fgColor = ['r' => $r, 'g' => $g, 'b' => $b];
        
        list($r, $g, $b) = sscanf($validated['bg_color'], "#%02x%02x%02x");
        $bgColor = ['r' => $r, 'g' => $g, 'b' => $b];
        
        // Update template - don't update logo_path if no new logo is uploaded
        $updateData = [
            'name' => $validated['name'],
            'fg_color' => json_encode($fgColor),
            'bg_color' => json_encode($bgColor),
            'logo_size' => $validated['logo_size'] ?? $template->logo_size ?? 60,
            'shape' => $validated['shape'],
            'error_correction' => $validated['error_correction'],
        ];
        
        $template->update($updateData);
        
        return redirect()->route('custom-qr.index')->with('success', 'Custom QR template updated successfully');
    }
    
    public function destroy($id)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $template = CustomQrTemplate::findOrFail($id);
        
        // Delete logo if exists
        if ($template->logo_path) {
            Storage::delete($template->logo_path);
        }
        
        $template->delete();
        
        return redirect()->route('custom-qr.index')->with('success', 'Custom QR template deleted successfully');
    }
    
    public function preview($id, Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        try {
            $template = CustomQrTemplate::findOrFail($id);
            $sampleData = $request->input('data', 'https://example.com/sample-qr-code');
            
            \Log::info('Previewing QR code for template: ' . $id);
            
            // Make sure template settings are up to date with logo
            if ($template->logo_path && !$this->isLogoInSettings($template)) {
                $this->appendLogoToSettings($template);
                // Reload the template with updated settings
                $template = CustomQrTemplate::findOrFail($id);
            }
            
            // If format=raw is specified AND the request is explicit about it, return the raw QR code image
            if ($request->has('format') && $request->input('format') === 'raw') {
                $format = $request->input('type', 'png');
                
                // Create QR code with appropriate error correction level
                $qrCode = \Endroid\QrCode\QrCode::create($sampleData)
                    ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                    ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
                    ->setSize(300)
                    ->setMargin(10);
                
                // Set foreground and background colors
                $fgColor = json_decode($template->fg_color, true);
                $bgColor = json_decode($template->bg_color, true);
                
                if ($fgColor && is_array($fgColor)) {
                    $qrCode->setForegroundColor(new \Endroid\QrCode\Color\Color($fgColor['r'], $fgColor['g'], $fgColor['b']));
                }
                
                if ($bgColor && is_array($bgColor)) {
                    $qrCode->setBackgroundColor(new \Endroid\QrCode\Color\Color($bgColor['r'], $bgColor['g'], $bgColor['b']));
                }
                
                // Choose writer based on format
                $writer = strtolower($format) === 'svg' 
                    ? new \Endroid\QrCode\Writer\SvgWriter() 
                    : new \Endroid\QrCode\Writer\PngWriter();
                
                // Add logo if template has one
                $result = null;
                if ($template->logo_path && \Illuminate\Support\Facades\Storage::exists($template->logo_path)) {
                    $logoPath = \Illuminate\Support\Facades\Storage::path($template->logo_path);
                    $logo = \Endroid\QrCode\Logo\Logo::create($logoPath)
                        ->setResizeToWidth(isset($template->logo_size) ? (int)$template->logo_size : 80);
                    
                    $result = $writer->write($qrCode, $logo);
                } else {
                    $result = $writer->write($qrCode);
                }
                
                return response($result->getString())
                    ->header('Content-Type', $result->getMimeType())
                    ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
            }
            
            // Always return a view that will use qr-code-styling library
            return view('custom-qr.preview', [
                'template' => $template,
                'sampleData' => $sampleData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error previewing QR code: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Create a basic QR code as fallback
            $qrCode = \Endroid\QrCode\QrCode::create('Error: ' . substr($e->getMessage(), 0, 30))
                ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High);
                
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $result = $writer->write($qrCode);
            
            return response($result->getString())
                ->header('Content-Type', $result->getMimeType())
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }
    }
    
    public function generateQrForGuest($guestId, $templateId)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $invitation = Invitation::findOrFail($guestId);
        $template = CustomQrTemplate::findOrFail($templateId);
        
        // Generate QR data - use just the code instead of the full URL
        $qrData = $invitation->qrcode_invitation;
        
        // Create QR code with appropriate error correction level
        $qrCode = \Endroid\QrCode\QrCode::create($qrData)
            ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
            ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10);
        
        // Set foreground and background colors
        $fgColor = json_decode($template->fg_color, true);
        $bgColor = json_decode($template->bg_color, true);
        
        if ($fgColor && is_array($fgColor)) {
            $qrCode->setForegroundColor(new \Endroid\QrCode\Color\Color($fgColor['r'], $fgColor['g'], $fgColor['b']));
        }
        
        if ($bgColor && is_array($bgColor)) {
            $qrCode->setBackgroundColor(new \Endroid\QrCode\Color\Color($bgColor['r'], $bgColor['g'], $bgColor['b']));
        }
        
        // Create writer
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        
        // Add logo if template has one
        $result = null;
        if ($template->logo_path && \Illuminate\Support\Facades\Storage::exists($template->logo_path)) {
            $logoPath = \Illuminate\Support\Facades\Storage::path($template->logo_path);
            $logo = \Endroid\QrCode\Logo\Logo::create($logoPath)
                ->setResizeToWidth(isset($template->logo_size) ? (int)$template->logo_size : 80);
            
            $result = $writer->write($qrCode, $logo);
        } else {
            $result = $writer->write($qrCode);
        }
        
        // Save the QR code
        $qrImagePath = 'public/img/qrCode/custom/' . $invitation->qrcode_invitation . '.png';
        \Illuminate\Support\Facades\Storage::put($qrImagePath, $result->getString());
        
        // Also save to public directory for backward compatibility
        \Illuminate\Support\Facades\File::ensureDirectoryExists(public_path('/img/qrCode/'));
        file_put_contents(public_path('/img/qrCode/' . $invitation->qrcode_invitation . '.png'), $result->getString());
        
        // Update invitation with custom QR path
        $invitation->update([
            'custom_qr_path' => $qrImagePath,
            'custom_qr_template_id' => $template->id
        ]);
        
        return redirect()->back()->with('success', 'Custom QR code generated successfully');
    }
    
    protected function generateCustomQr($data, $template, $format = 'png')
    {
        // If the template has settings_json, use that for advanced styling via JavaScript
        if ($template->settings_json) {
            \Log::info('Using settings_json for template #' . $template->id . ': ' . $template->settings_json);
            return $this->generateAdvancedQr($data, $template, $format);
        }
        
        \Log::info('Using legacy QR generation for template #' . $template->id);
        
        // Legacy code for backward compatibility with old template formats
        
        // Create QR code with appropriate error correction level
        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10);
        
        // Handle colors
        $fgColor = json_decode($template->fg_color, true);
        $bgColor = json_decode($template->bg_color, true);
        
        if ($fgColor && is_array($fgColor)) {
            $qrCode->setForegroundColor(new Color($fgColor['r'], $fgColor['g'], $fgColor['b']));
        } else {
            $qrCode->setForegroundColor(new Color(0, 0, 0));
        }
        
        if ($bgColor && is_array($bgColor)) {
            $qrCode->setBackgroundColor(new Color($bgColor['r'], $bgColor['g'], $bgColor['b']));
        } else {
            $qrCode->setBackgroundColor(new Color(255, 255, 255));
        }
        
        // Choose writer based on format
        if (strtolower($format) === 'svg') {
            $writer = new SvgWriter();
        } else {
            $writer = new PngWriter();
        }
        
        // Add logo if template has one
        if ($template->logo_path && Storage::exists($template->logo_path)) {
            $logoPath = Storage::path($template->logo_path);
            $logo = Logo::create($logoPath)
                ->setResizeToWidth(isset($template->logo_size) ? (int)$template->logo_size : 80);
            
            return $writer->write($qrCode, $logo);
        }
        
        return $writer->write($qrCode);
    }
    
    /**
     * Generate advanced QR code using settings_json
     * This uses Intervention Image to generate QR based on the saved JSON settings
     */
    protected function generateAdvancedQr($data, $template, $format = 'png')
    {
        try {
            // Parse saved settings
            $settings = json_decode($template->settings_json, true);
            if (!$settings) {
                \Log::error('Failed to parse settings_json for template #' . $template->id . ': ' . $template->settings_json);
                throw new \Exception('Invalid JSON settings');
            }
            
            // Add the data to the settings
            $settings['data'] = $data;
            
            // Determine the format to use (match the editor if specified in settings)
            if (isset($settings['type']) && in_array(strtolower($settings['type']), ['svg', 'png'])) {
                $format = strtolower($settings['type']);
                \Log::info("Using format from settings: $format");
            }
            
            \Log::info('Generating advanced QR with settings: ' . json_encode($settings) . ' in format: ' . $format);
            
            // Use base Endroid QR as our starting point
            $qrCode = QrCode::create($data)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(isset($settings['qrOptions']['errorCorrectionLevel']) 
                    ? $this->getErrorCorrectionLevel($settings['qrOptions']['errorCorrectionLevel']) 
                    : ErrorCorrectionLevel::High)
                ->setSize(isset($settings['width']) ? (int)$settings['width'] : 300)
                ->setMargin(isset($settings['margin']) ? (int)$settings['margin'] : 10);
            
            // Choose writer based on format
            if (strtolower($format) === 'svg') {
                $writer = new \Endroid\QrCode\Writer\SvgWriter();
            } else {
                $writer = new PngWriter();
            }
            
            // Shape handling - This is limited in Endroid QR but we can try some customization
            $shapeOption = null;
            if (isset($settings['dotsOptions']['type'])) {
                $shapeOption = $settings['dotsOptions']['type'];
                \Log::info("Shape option found: $shapeOption - Note: Limited support in backend renderer");
            }
            
            // Apply foreground color if available
            if (isset($settings['dotsOptions']) && isset($settings['dotsOptions']['color'])) {
                $hexColor = $settings['dotsOptions']['color'];
                if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $hexColor, $matches)) {
                    $r = hexdec($matches[1]);
                    $g = hexdec($matches[2]);
                    $b = hexdec($matches[3]);
                    $qrCode->setForegroundColor(new Color($r, $g, $b));
                    \Log::info("Applied foreground color: $hexColor ($r,$g,$b)");
                }
            } elseif (isset($settings['dotsOptions']['gradient'])) {
                // For gradient, use the first color as foreground
                if (isset($settings['dotsOptions']['gradient']['colorStops'][0]['color'])) {
                    $hexColor = $settings['dotsOptions']['gradient']['colorStops'][0]['color'];
                    if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $hexColor, $matches)) {
                        $r = hexdec($matches[1]);
                        $g = hexdec($matches[2]);
                        $b = hexdec($matches[3]);
                        $qrCode->setForegroundColor(new Color($r, $g, $b));
                        \Log::info("Applied gradient foreground color: $hexColor ($r,$g,$b)");
                    }
                }
            }
            
            // Apply background color if available
            if (isset($settings['backgroundOptions']) && isset($settings['backgroundOptions']['color'])) {
                $hexColor = $settings['backgroundOptions']['color'];
                if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $hexColor, $matches)) {
                    $r = hexdec($matches[1]);
                    $g = hexdec($matches[2]);
                    $b = hexdec($matches[3]);
                    $qrCode->setBackgroundColor(new Color($r, $g, $b));
                    \Log::info("Applied background color: $hexColor ($r,$g,$b)");
                }
            } elseif (isset($settings['backgroundOptions']['gradient'])) {
                // For gradient, use the first color as background
                if (isset($settings['backgroundOptions']['gradient']['colorStops'][0]['color'])) {
                    $hexColor = $settings['backgroundOptions']['gradient']['colorStops'][0]['color'];
                    if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $hexColor, $matches)) {
                        $r = hexdec($matches[1]);
                        $g = hexdec($matches[2]);
                        $b = hexdec($matches[3]);
                        $qrCode->setBackgroundColor(new Color($r, $g, $b));
                        \Log::info("Applied gradient background color: $hexColor ($r,$g,$b)");
                    }
                }
            }
            
            $writer = new PngWriter();
            
            // Add logo if present in settings or template
            $logoPath = null;
            $logoSize = 80; // Default
            
            // Check if we have logo in the settings
            if (isset($settings['image']) && !empty($settings['image'])) {
                $logoPath = $this->getPathFromUrl($settings['image']);
                \Log::info('Using logo from settings: ' . $settings['image']);
                \Log::info('Converted logo path: ' . $logoPath);
                
                // Logo sizes from settings
                if (isset($settings['imageOptions']) && isset($settings['imageOptions']['imageSize'])) {
                    // imageSize in settings is a percent (0.1 to 0.5)
                    $sizePercentage = (float)$settings['imageOptions']['imageSize'];
                    $baseSize = isset($settings['width']) ? (int)$settings['width'] : 300;
                    $logoSize = (int)($sizePercentage * $baseSize);
                    \Log::info("Using logo size from settings: $sizePercentage (calculated to ${logoSize}px)");
                }
            } 
            // Check if the template has a logo
            elseif ($template->logo_path && Storage::exists($template->logo_path)) {
                $logoPath = Storage::path($template->logo_path);
                \Log::info('Using logo from template: ' . $logoPath);
                
                if ($template->logo_size) {
                    $logoSize = (int)$template->logo_size;
                    \Log::info("Using logo size from template: ${logoSize}px");
                }
            }
            
            if ($logoPath && file_exists($logoPath)) {
                \Log::info('Logo file exists at path: ' . $logoPath);
                
                // Create and configure the logo
                $logo = Logo::create($logoPath)
                    ->setResizeToWidth($logoSize);
                
                // Add logo margin if specified
                if (isset($settings['imageOptions']) && isset($settings['imageOptions']['margin'])) {
                    $margin = (int)$settings['imageOptions']['margin'];
                    if ($margin > 0) {
                        \Log::info("Setting logo margin to: {$margin}px");
                        // Endroid doesn't support logo margin directly, but we can approximate
                        // by reducing logo size proportionally
                        $adjustedSize = $logoSize - (2 * $margin);
                        if ($adjustedSize > 10) { // Ensure reasonable size
                            $logo->setResizeToWidth($adjustedSize);
                            \Log::info("Adjusted logo size to account for margin: {$adjustedSize}px");
                        }
                    }
                }
                
                // Create QR with logo
                $result = $writer->write($qrCode, $logo);
                \Log::info('Successfully created QR with logo');
                return $result;
            } else {
                if ($logoPath) {
                    \Log::warning('Logo file not found at path: ' . $logoPath);
                } else {
                    \Log::info('No logo path found for template');
                }
                
                // Basic QR without logo
                $result = $writer->write($qrCode);
                \Log::info('Created QR without logo');
                return $result;
            }
            
        } catch (\Exception $e) {
            // Log error and fall back to basic QR code
            \Log::error('Failed to generate advanced QR: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Create a basic QR code as fallback
            $qrCode = QrCode::create($data)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::High);
                
            $writer = new PngWriter();
            return $writer->write($qrCode);
        }
    }
    
    /**
     * Helper method to get the correct ErrorCorrectionLevel enum value
     * 
     * @param string $level The error correction level (L,M,Q,H)
     * @return ErrorCorrectionLevel
     */
    private function getErrorCorrectionLevel($level) 
    {
        // Normalize to uppercase single letter
        $level = strtoupper(substr(trim($level), 0, 1));
        
        switch ($level) {
            case 'L':
                return ErrorCorrectionLevel::Low;
            case 'M':
                return ErrorCorrectionLevel::Medium;
            case 'Q':
                return ErrorCorrectionLevel::Quartile;
            case 'H':
            default:
                return ErrorCorrectionLevel::High;
        }
    }
    
    /**
     * Helper method to convert a URL to a file path
     */
    protected function getPathFromUrl($url) 
    {
        // Handle base64 data URLs
        if (strpos($url, 'data:image') === 0) {
            try {
                $tempFile = tempnam(sys_get_temp_dir(), 'logo');
                $parts = explode(',', $url);
                if (count($parts) > 1) {
                    $data = $parts[1];
                    file_put_contents($tempFile, base64_decode($data));
                    \Log::info('Created temporary file for base64 image: ' . $tempFile);
                    return $tempFile;
                }
            } catch (\Exception $e) {
                \Log::error('Error creating temp file from base64: ' . $e->getMessage());
                return null;
            }
        }
        
        // Handle storage URLs
        $storageUrl = url('storage');
        if (strpos($url, $storageUrl) === 0) {
            try {
                $relativePath = str_replace($storageUrl, '', $url);
                $fullPath = storage_path('app/public' . $relativePath);
                \Log::info('Converted storage URL to path: ' . $fullPath);
                
                if (file_exists($fullPath)) {
                    return $fullPath;
                } else {
                    \Log::warning('Storage file does not exist: ' . $fullPath);
                }
            } catch (\Exception $e) {
                \Log::error('Error converting storage URL to path: ' . $e->getMessage());
            }
        }
        
        // Check if it's a direct path in public/storage
        if (strpos($url, '/custom-qr/') !== false) {
            try {
                $path = public_path('storage' . substr($url, strpos($url, '/custom-qr/')));
                if (file_exists($path)) {
                    \Log::info('Found file in public storage: ' . $path);
                    return $path;
                }
            } catch (\Exception $e) {
                \Log::error('Error finding file in public storage: ' . $e->getMessage());
            }
        }
        
        \Log::warning('Could not convert URL to path: ' . $url);
        return null;
    }
    
    // New method to create branded QR code with more advanced styling
    public function createBrandedQr(Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        return view('custom-qr.branded');
    }
    
    // Store a new branded QR template
    public function storeBrandedQr(Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_logo' => 'required|image|max:2048',
            'main_color' => 'required|string',
            'secondary_color' => 'nullable|string',
            'background_color' => 'required|string',
            'qr_shape' => 'required|in:square,rounded,circle,custom',
            'finder_pattern_style' => 'required|in:default,rounded,dot,custom',
            'show_finder_pattern' => 'nullable|in:0,1',
        ]);
        
        // Process the brand logo
        $logoPath = $request->file('brand_logo')->store('public/custom-qr/logos');
        
        // Convert hex colors to RGB
        list($r, $g, $b) = sscanf($validated['main_color'], "#%02x%02x%02x");
        $mainColor = ['r' => $r, 'g' => $g, 'b' => $b];
        
        $secondaryColor = null;
        if ($validated['secondary_color']) {
            list($r, $g, $b) = sscanf($validated['secondary_color'], "#%02x%02x%02x");
            $secondaryColor = ['r' => $r, 'g' => $g, 'b' => $b];
        }
        
        list($r, $g, $b) = sscanf($validated['background_color'], "#%02x%02x%02x");
        $bgColor = ['r' => $r, 'g' => $g, 'b' => $b];
        
        // Create template with advanced styling properties
        $template = CustomQrTemplate::create([
            'name' => $validated['name'],
            'fg_color' => json_encode($mainColor),
            'bg_color' => json_encode($bgColor),
            'secondary_color' => $secondaryColor ? json_encode($secondaryColor) : null,
            'logo_path' => $logoPath,
            'logo_size' => $request->input('logo_size', 80),
            'shape' => $validated['qr_shape'],
            'finder_pattern_style' => $validated['finder_pattern_style'],
            'is_branded' => true,
            'error_correction' => 'H', // Always use high error correction for branded QR codes
            'show_finder_pattern' => $request->input('show_finder_pattern', '1'),
        ]);
        
        return redirect()->route('custom-qr.index')->with('success', 'Branded QR template created successfully');
    }
    
    // Generate and display a preview of a branded QR code
    public function previewBrandedQr(Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        try {
            // Ensure we have a valid file
            if (!$request->hasFile('brand_logo')) {
                throw new \Exception('Brand logo file is missing.');
            }
            
            if (!$request->file('brand_logo')->isValid()) {
                throw new \Exception('Invalid brand logo file upload.');
            }
            
            // Log for debugging
            \Log::info('QR Preview - Request fields: ' . json_encode($request->except('brand_logo')));
            \Log::info('QR Preview - Logo file info: ' . json_encode([
                'name' => $request->file('brand_logo')->getClientOriginalName(),
                'mime' => $request->file('brand_logo')->getMimeType(),
                'size' => $request->file('brand_logo')->getSize(),
            ]));
            
            // Process the test data
            $validated = $request->validate([
                'test_data' => 'nullable|string',
                'brand_logo' => 'required|image|max:2048',
                'main_color' => 'required|string',
                'secondary_color' => 'nullable|string',
                'background_color' => 'required|string',
                'qr_shape' => 'required|in:square,rounded,circle,custom',
                'finder_pattern_style' => 'required|in:default,rounded,dot,custom',
                'logo_size' => 'nullable|integer|min:30|max:150',
                'show_finder_pattern' => 'nullable|in:0,1',
            ]);
            
            // Create a temporary template object
            $template = new \stdClass();
            
            // Process the brand logo with better error handling
            $logoFile = $request->file('brand_logo');
            $logoName = uniqid() . '.' . $logoFile->getClientOriginalExtension();
            $logoPath = 'public/custom-qr/temp/' . $logoName;
            
            // Make sure the directory exists
            if (!Storage::exists('public/custom-qr/temp')) {
                Storage::makeDirectory('public/custom-qr/temp');
            }
            
            // Save the file using direct file put
            $uploaded = Storage::put($logoPath, file_get_contents($logoFile->getRealPath()));
            
            if (!$uploaded) {
                throw new \Exception('Failed to save logo file to storage.');
            }
            
            $template->logo_path = $logoPath;
            
            // Parse color values with better validation
            $main_color = $validated['main_color'];
            $background_color = $validated['background_color'];
            
            // Normalize colors (add # if missing)
            $main_color = str_starts_with($main_color, '#') ? $main_color : '#' . $main_color;
            $background_color = str_starts_with($background_color, '#') ? $background_color : '#' . $background_color;
            
            // Extract RGB components safely
            $r = $g = $b = 0;
            if (sscanf($main_color, "#%02x%02x%02x", $r, $g, $b) !== 3) {
                throw new \Exception('Invalid main color format: ' . $main_color);
            }
            $mainColor = ['r' => $r, 'g' => $g, 'b' => $b];
            $template->fg_color = json_encode($mainColor);
            
            $r = $g = $b = 255; // Default to white
            if (sscanf($background_color, "#%02x%02x%02x", $r, $g, $b) !== 3) {
                throw new \Exception('Invalid background color format: ' . $background_color);
            }
            $bgColor = ['r' => $r, 'g' => $g, 'b' => $b];
            $template->bg_color = json_encode($bgColor);
            
            // Set other template properties
            $template->logo_size = $request->input('logo_size', 80);
            $template->error_correction = 'H';
            $template->shape = $validated['qr_shape'];
            $template->finder_pattern_style = $validated['finder_pattern_style'];
            $template->show_finder_pattern = $request->input('show_finder_pattern', '1');
            
            // Generate QR with the data
            $testData = $validated['test_data'] ?? 'https://example.com/sample-branded-qr-code';
            $qrCode = $this->generateCustomQr($testData, $template);
            
            // Delete temporary logo after generating QR code
            if (Storage::exists($logoPath)) {
                Storage::delete($logoPath);
            }
            
            // Return the QR code as an image
            return response($qrCode->getString())
                ->header('Content-Type', $qrCode->getMimeType())
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Content-Disposition', 'inline; filename="qr-preview.png"');
                
        } catch (\Exception $e) {
            // Log the error with details
            \Log::error('QR Preview Error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            
            // Return JSON error for AJAX handling
            return response()->json(['error' => 'Error generating QR code: ' . $e->getMessage()], 422);
        }
    }
    
    /**
     * Create a fully branded QR code that integrates the logo with QR pattern
     */
    public function advancedBrandedQr()
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        return view('custom-qr.advanced-branded');
    }
    
    /**
     * Preview the advanced branded QR
     */
    public function previewAdvancedBrandedQr(Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $validated = $request->validate([
            'brand_logo' => 'required|image|max:2048',
            'primary_color' => 'required|string',
            'secondary_color' => 'required|string',
            'background_color' => 'required|string',
            'brand_theme' => 'required|in:corporate,playful,minimal,bold',
            'qr_data' => 'nullable|string',
        ]);
        
        try {
            // Store the logo temporarily
            $logoPath = $request->file('brand_logo')->store('public/custom-qr/temp');
            $logoFullPath = storage_path('app/' . $logoPath);
            
            // Get QR data or use a placeholder
            $qrData = $validated['qr_data'] ?? 'https://example.com/branded-qr';
            
            // Generate a basic QR code first
            $writer = new PngWriter();
            $qrCode = QrCode::create($qrData)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
                ->setSize(300)
                ->setMargin(10);
                
            // Generate QR
            $result = $writer->write($qrCode);
            
            // Now create enhanced branded version using Intervention/Image
            // PERHATIAN: Kode di bawah ini menggunakan Intervention Image dengan sintaks lama
            // Jika dibutuhkan, perlu diupdate sesuai dengan versi Intervention Image 3.x
            // $qrImage = Image::make($result->getString());
            
            // Now create enhanced branded version using Intervention/Image
            $qrImage = Image::make($result->getString());
            $logoImage = Image::make($logoFullPath);
            
            // Apply themed branding based on theme selection
            $canvas = $this->createBrandedQrByTheme(
                $qrImage,
                $logoImage,
                $validated['brand_theme'],
                $validated['primary_color'],
                $validated['secondary_color'],
                $validated['background_color']
            );
            
            // Cleanup temporary files
            Storage::delete($logoPath);
            
            // Return the QR code as an image
            return response($canvas->encode('png'))
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Content-Disposition', 'inline; filename="qr-preview.png"');
                
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate preview: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Process and save the advanced branded QR code
     */
    public function processAdvancedBrandedQr(Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_logo' => 'required|image|max:2048',
            'primary_color' => 'required|string',
            'secondary_color' => 'required|string',
            'background_color' => 'required|string',
            'brand_theme' => 'required|in:corporate,playful,minimal,bold',
            'qr_data' => 'nullable|string',
        ]);
        
        try {
            // Process the brand logo
            $logoPath = $request->file('brand_logo')->store('public/custom-qr/logos');
            $logoFullPath = storage_path('app/' . $logoPath);
            
            // Convert colors from hex to RGB
            list($r, $g, $b) = sscanf($validated['primary_color'], "#%02x%02x%02x");
            $primaryColor = ['r' => $r, 'g' => $g, 'b' => $b];
            
            list($r, $g, $b) = sscanf($validated['secondary_color'], "#%02x%02x%02x");
            $secondaryColor = ['r' => $r, 'g' => $g, 'b' => $b];
            
            list($r, $g, $b) = sscanf($validated['background_color'], "#%02x%02x%02x");
            $bgColor = ['r' => $r, 'g' => $g, 'b' => $b];
            
            // Create template and save to database
            $template = CustomQrTemplate::create([
                'name' => $validated['name'],
                'fg_color' => json_encode($primaryColor),
                'bg_color' => json_encode($bgColor),
                'secondary_color' => json_encode($secondaryColor),
                'logo_path' => $logoPath,
                'logo_size' => 80, // Default logo size
                'shape' => 'custom',
                'finder_pattern_style' => 'custom',
                'show_finder_pattern' => true,
                'error_correction' => 'H', // Always high for complex designs
                'is_branded' => true,
                'is_advanced_branded' => true, // Flag this as an advanced branded QR
                'brand_theme' => $validated['brand_theme'],
            ]);
            
            // Generate a sample QR and save it as a reference
            $sampleQrData = $validated['qr_data'] ?? 'https://example.com/branded-sample';
            
            // Generate the base QR code
            $writer = new PngWriter();
            $qrCode = QrCode::create($sampleQrData)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::from('high'))
                ->setSize(300)
                ->setMargin(10);
                
            // Generate the basic QR (we'll use this as a base for our transformation)
            $result = $writer->write($qrCode);
            
            // Create branded QR for sample
            $image = Image::make($result->getString());
            $logoImage = Image::make($logoFullPath);
            
            // Process according to selected theme
            $themeResult = $this->createBrandedQrByTheme(
                $image, 
                $logoImage, 
                $validated['brand_theme'], 
                $validated['primary_color'],
                $validated['secondary_color'],
                $validated['background_color']
            );
            
            // Save the branded QR sample
            $samplePath = 'public/custom-qr/samples/' . $template->id . '.png';
            Storage::put($samplePath, (string)$themeResult->encode('png'));
            
            // Update the template with the sample path
            $template->update([
                'sample_qr_path' => $samplePath
            ]);
            
            return redirect()->route('custom-qr.index')->with('success', 'Advanced branded QR template created successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create advanced branded QR: ' . $e->getMessage());
        }
    }
    
    /**
     * Apply brand theme to QR code
     * 
     * @param \Intervention\Image\Image $qrImage Base QR code image
     * @param \Intervention\Image\Image $logoImage Logo image
     * @param string $theme Theme style (corporate, playful, minimal, bold)
     * @param string $primaryColor Hex color
     * @param string $secondaryColor Hex color
     * @param string $backgroundColor Hex color
     * @return \Intervention\Image\Image
     */
    protected function applyBrandTheme($qrImage, $logoImage, $theme, $primaryColor, $secondaryColor, $backgroundColor)
    {
        // Extract colors without # prefix for Intervention Image
        $primaryColor = ltrim($primaryColor, '#');
        $secondaryColor = ltrim($secondaryColor, '#');
        $backgroundColor = ltrim($backgroundColor, '#');
        
        // Get image dimensions
        $width = $qrImage->width();
        $height = $qrImage->height();
        
        // Create a new canvas with the background color
        $canvas = Image::canvas($width, $height, '#' . $backgroundColor);
        
        // Apply different styling based on theme
        switch ($theme) {
            case 'corporate':
                // Corporate theme - clean, professional style
                // Set QR code to primary color
                $qrImage->colorize(100, 100, 100); // Increase contrast
                
                // Create a mask from the QR code (black parts will be where QR is)
                $mask = clone $qrImage;
                $mask->greyscale()->brightness(-10)->contrast(100);
                
                // Insert QR onto canvas with primary color
                $canvas->mask($mask, false);
                $canvas->fill('#' . $primaryColor);
                
                // Prepare logo with a slight drop shadow for professional look
                $logoResized = clone $logoImage;
                $logoResized->resize($width/4, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Add a subtle professional frame
                $canvas->rectangle(5, 5, $width-10, $height-10, function ($draw) use ($primaryColor) {
                    $draw->border(2, '#' . $primaryColor);
                });
                
                // Put logo in center
                $canvas->insert($logoResized, 'center');
                break;
                
            case 'playful':
                // Playful theme - more rounded, fun style
                // Apply a gradient background
                $canvas->fill('#' . $backgroundColor);
                
                // Create QR mask
                $mask = clone $qrImage;
                $mask->greyscale()->brightness(-10)->contrast(100);
                
                // Add colorful QR to canvas
                $canvas->mask($mask, false);
                $canvas->fill('#' . $primaryColor);
                
                // Make QR dots more rounded by applying blur and contrast
                // $canvas->blur(1)->brightness(5)->contrast(20);
                $canvas->brightness(5)->contrast(20);
                
                // Create playful border
                $canvas->rectangle(0, 0, $width-1, $height-1, function ($draw) use ($secondaryColor) {
                    $draw->border(4, '#' . $secondaryColor);
                });
                
                // Prepare logo
                $logoResized = clone $logoImage;
                $logoResized->resize($width/3, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Add logo with slight rotation for playful effect
                $logoResized->rotate(5);
                $canvas->insert($logoResized, 'center');
                break;
                
            case 'minimal':
                // Minimal theme - clean, simple, elegant
                // Start with a clean canvas
                $canvas->fill('#' . $backgroundColor);
                
                // Create QR mask with simplified dots
                $mask = clone $qrImage;
                $mask->greyscale()->brightness(-10)->contrast(100);
                
                // Apply minimal styling to QR
                $canvas->mask($mask, false);
                $canvas->fill('#' . $primaryColor);
                
                // Add thin border
                $canvas->rectangle(0, 0, $width-1, $height-1, function ($draw) use ($primaryColor) {
                    $draw->border(1, '#' . $primaryColor);
                });
                
                // Simplify and resize logo
                $logoResized = clone $logoImage;
                $logoResized->resize($width/4, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Calculate center area size based on logo size with extra padding
                $logoWidth = $logoResized->width();
                $logoHeight = $logoResized->height();
                $centerSize = max($logoWidth, $logoHeight) * 1.6; // Increase clear area around logo
                
                // Create clean circular space in center (Starbucks style)
                $logoBackground = Image::canvas(
                    $centerSize, $centerSize, '#' . $backgroundColor
                );
                
                // Create perfect circle for clear background
                $radius = ($centerSize - 4) / 2;
                $centerPoint = $centerSize / 2;
                $logoBackground->circle($centerSize - 4, $centerPoint, $centerPoint, function ($draw) use ($backgroundColor) {
                    $draw->background('#' . $backgroundColor);
                });
                
                // Add white circular background behind logo
                $canvas->insert($logoBackground, 'center');
                
                // Center logo inside the clear area
                $canvas->insert($logoResized, 'center');
                break;
                
            case 'bold':
                // Bold theme - strong visual impact like UPS
                // Create high contrast QR base
                $canvas->fill('#' . $primaryColor);
                
                // Create a strong mask
                $mask = clone $qrImage;
                $mask->greyscale()->brightness(-30)->contrast(100);
                
                // Apply strong QR pattern
                $canvas->mask($mask, false);
                
                // Add a bold border
                $canvas->rectangle(0, 0, $width-1, $height-1, function ($draw) use ($secondaryColor) {
                    $draw->border(8, '#' . $secondaryColor);
                });
                
                // Prepare logo with shield-like effect (similar to UPS)
                $logoResized = clone $logoImage;
                $logoResized->resize($width/3, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Create a background shape for logo
                $shieldWidth = $logoResized->width() + 20;
                $shieldHeight = $logoResized->height() + 20;
                $shield = Image::canvas($shieldWidth, $shieldHeight, '#' . $backgroundColor);
                $shield->rectangle(0, 0, $shieldWidth-1, $shieldHeight-1, function ($draw) use ($secondaryColor) {
                    $draw->background('#' . $backgroundColor);
                    $draw->border(4, '#' . $secondaryColor);
                });
                
                // Insert shield and logo
                $canvas->insert($shield, 'center');
                $canvas->insert($logoResized, 'center');
                break;
                
            default:
                // Default handling - just overlay the logo on QR
                $logoResized = clone $logoImage;
                $logoResized->resize($width/4, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $canvas->insert($qrImage, 'center');
                $canvas->insert($logoResized, 'center');
        }
        
        return $canvas;
    }
    
    /**
     * Create a branded QR code with advanced styling based on theme
     *
     * @param \Intervention\Image\Image $qrImage Base QR image
     * @param \Intervention\Image\Image $logoImage Logo image
     * @param string $theme Theme name (corporate, playful, minimal, bold)
     * @param string $primaryColor Primary color (hex)
     * @param string $secondaryColor Secondary color (hex)
     * @param string $backgroundColor Background color (hex)
     * @return \Intervention\Image\Image
     */
    protected function createBrandedQrByTheme($qrImage, $logoImage, $theme, $primaryColor, $secondaryColor, $backgroundColor)
    {
        // Remove # from hex colors if present
        $primaryColor = ltrim($primaryColor, '#');
        $secondaryColor = ltrim($secondaryColor, '#');
        $backgroundColor = ltrim($backgroundColor, '#');
        
        // Get image dimensions
        $width = $qrImage->width();
        $height = $qrImage->height();
        
        // Create a new canvas with background color
        $canvas = Image::canvas($width, $height, '#' . $backgroundColor);
        
        // Apply styling based on selected theme
        switch ($theme) {
            case 'corporate':
                // Corporate theme - professional, clean look (like BMW)
                
                // Create QR mask for applying colors
                $mask = clone $qrImage;
                $mask->greyscale()->brightness(-10)->contrast(100);
                
                // Add QR code to canvas
                $canvas->mask($mask, false);
                $canvas->fill('#' . $primaryColor);
                
                // Add elegant border
                $canvas->rectangle(8, 8, $width-9, $height-9, function ($draw) use ($secondaryColor) {
                    $draw->border(2, '#' . $secondaryColor);
                });
                
                // Add subtle corner elements for corporate feel
                $cornerSize = $width / 10;
                
                // Top-left corner
                $canvas->rectangle(5, 5, 5+$cornerSize, 5+$cornerSize, function ($draw) use ($secondaryColor) {
                    $draw->border(3, '#' . $secondaryColor);
                });
                
                // Bottom-right corner
                $canvas->rectangle($width-5-$cornerSize, $height-5-$cornerSize, $width-5, $height-5, function ($draw) use ($secondaryColor) {
                    $draw->border(3, '#' . $secondaryColor);
                });
                
                // Resize and center logo
                $logoResized = clone $logoImage;
                $logoResized->resize($width/4, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Create subtle shadow effect for logo
                $shadow = Image::canvas(
                    $logoResized->width() + 6, 
                    $logoResized->height() + 6, 
                    'rgba(0, 0, 0, 0.2)'
                );
                
                // Place shadow and logo
                $canvas->insert($shadow, 'center', 3, 3);
                $canvas->insert($logoResized, 'center');
                break;
                
            case 'playful':
                // Playful theme - fun, rounded elements (like Burger King)
                
                // Apply rounded corners to entire canvas
                $canvas = $canvas->roundCorners(15);
                
                // Create mask and apply colorful styling
                $mask = clone $qrImage;
                $mask->greyscale()->brightness(-10)->contrast(90);
                
                // Apply QR with primary color
                $canvas->mask($mask, false);
                $canvas->fill('#' . $primaryColor);
                
                // Soften the QR blocks for playful look
                $canvas->blur(1);
                $canvas->brightness(5);
                $canvas->contrast(15);
                
                // Add playful border
                $canvas->rectangle(5, 5, $width-6, $height-6, function ($draw) use ($secondaryColor) {
                    $draw->border(4, '#' . $secondaryColor);
                });
                
                // Make finder patterns more playful
                $this->customizeFinderPatterns($canvas, 'circle', $secondaryColor);
                
                // Add logo with fun twist
                $logoResized = clone $logoImage;
                $logoResized->resize($width/3, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Add slight rotation for playful effect
                $logoResized->rotate(rand(-7, 7));
                
                // Create a fun background for logo
                $logoBackground = Image::canvas(
                    $logoResized->width() + 20, 
                    $logoResized->height() + 20, 
                    '#' . $backgroundColor
                );
                $logoBackground->roundCorners(10);
                
                // Add logo with background
                $canvas->insert($logoBackground, 'center');
                $canvas->insert($logoResized, 'center');
                break;
                
            case 'minimal':
                // Minimal theme - clean, simple, elegant
                // Start with a clean canvas
                $canvas->fill('#' . $backgroundColor);
                
                // Create QR mask with simplified dots
                $mask = clone $qrImage;
                $mask->greyscale()->brightness(-10)->contrast(100);
                
                // Apply minimal styling to QR
                $canvas->mask($mask, false);
                $canvas->fill('#' . $primaryColor);
                
                // Add thin border
                $canvas->rectangle(0, 0, $width-1, $height-1, function ($draw) use ($primaryColor) {
                    $draw->border(1, '#' . $primaryColor);
                });
                
                // Simplify and resize logo
                $logoResized = clone $logoImage;
                $logoResized->resize($width/4, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Calculate center area size based on logo size with extra padding
                $logoWidth = $logoResized->width();
                $logoHeight = $logoResized->height();
                $centerSize = max($logoWidth, $logoHeight) * 1.6; // Increase clear area around logo
                
                // Create clean circular space in center (Starbucks style)
                $logoBackground = Image::canvas(
                    $centerSize, $centerSize, '#' . $backgroundColor
                );
                
                // Create perfect circle for clear background
                $radius = ($centerSize - 4) / 2;
                $centerPoint = $centerSize / 2;
                $logoBackground->circle($centerSize - 4, $centerPoint, $centerPoint, function ($draw) use ($backgroundColor) {
                    $draw->background('#' . $backgroundColor);
                });
                
                // Add white circular background behind logo
                $canvas->insert($logoBackground, 'center');
                
                // Center logo inside the clear area
                $canvas->insert($logoResized, 'center');
                break;
                
            case 'bold':
                // Bold theme - strong visual impact (like UPS)
                
                // Create bold contrast QR
                $mask = clone $qrImage;
                $mask->greyscale()->brightness(-20)->contrast(100);
                
                // Apply strong styling
                $canvas->fill('#' . $primaryColor);
                $canvas->mask($mask, false);
                
                // Add thick border for bold look
                $canvas->rectangle(0, 0, $width-1, $height-1, function ($draw) use ($secondaryColor) {
                    $draw->border(8, '#' . $secondaryColor);
                });
                
                // Make finder patterns stand out
                $this->customizeFinderPatterns($canvas, 'square', $secondaryColor, 4);
                
                // Prepare logo with shield-like effect (similar to UPS)
                $logoResized = clone $logoImage;
                $logoResized->resize($width/3, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                
                // Create a shield/badge for logo
                $shieldWidth = $logoResized->width() + 20;
                $shieldHeight = $logoResized->height() + 20;
                $shield = Image::canvas($shieldWidth, $shieldHeight, '#' . $backgroundColor);
                $shield->rectangle(0, 0, $shieldWidth-1, $shieldHeight-1, function ($draw) use ($secondaryColor) {
                    $draw->background('#' . $backgroundColor);
                    $draw->border(4, '#' . $secondaryColor);
                });
                
                // Add shield and logo
                $canvas->insert($shield, 'center');
                $canvas->insert($logoResized, 'center');
                break;
                
            default:
                // Basic fallback - simple branded QR
                $logoResized = clone $logoImage;
                $logoResized->resize($width/4, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $canvas->insert($qrImage, 'center');
                $canvas->insert($logoResized, 'center');
        }
        
        return $canvas;
    }
    
    /**
     * Customize finder patterns (the three squares in corners of QR code)
     *
     * @param \Intervention\Image\Image $image QR image
     * @param string $style Style (square, circle)
     * @param string $color Color (hex without #)
     * @param int $weight Line weight
     * @return void
     */
    protected function customizeFinderPatterns($image, $style, $color, $weight = 2)
    {
        $width = $image->width();
        $size = $width / 7; // Approximate size of finder pattern
        
        // Position of the three finder patterns (top-left, top-right, bottom-left)
        $positions = [
            ['x' => $size, 'y' => $size],
            ['x' => $width - $size, 'y' => $size],
            ['x' => $size, 'y' => $width - $size]
        ];
        
        foreach ($positions as $pos) {
            if ($style == 'circle') {
                // Draw circular finder pattern
                $image->circle($size * 1.5, $pos['x'], $pos['y'], function ($draw) use ($color, $weight) {
                    $draw->border($weight, '#' . $color);
                });
                
                // Inner circle
                $image->circle($size * 0.8, $pos['x'], $pos['y'], function ($draw) use ($color, $weight) {
                    $draw->border($weight, '#' . $color);
                });
            } else {
                // Draw square finder pattern (default)
                $halfSize = $size * 0.75;
                $image->rectangle(
                    $pos['x'] - $halfSize, 
                    $pos['y'] - $halfSize, 
                    $pos['x'] + $halfSize, 
                    $pos['y'] + $halfSize, 
                    function ($draw) use ($color, $weight) {
                        $draw->border($weight, '#' . $color);
                    }
                );
                
                // Inner square
                $innerSize = $size * 0.4;
                $image->rectangle(
                    $pos['x'] - $innerSize, 
                    $pos['y'] - $innerSize, 
                    $pos['x'] + $innerSize, 
                    $pos['y'] + $innerSize, 
                    function ($draw) use ($color, $weight) {
                        $draw->border($weight, '#' . $color);
                    }
                );
            }
        }
    }
    
    /**
     * Edit a branded QR code template
     * 
     * @param int $id Template ID
     * @return \Illuminate\View\View
     */
    public function editBranded($id)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $template = CustomQrTemplate::findOrFail($id);
        
        // If it's not a branded QR code or it's an advanced branded QR, redirect to the appropriate editor
        if (!$template->is_branded) {
            return redirect()->route('custom-qr.edit', $id);
        }
        
        if ($template->is_advanced_branded) {
            return redirect()->route('custom-qr.editAdvancedBranded', $id);
        }
        
        return view('custom-qr.edit-branded', compact('template'));
    }
    
    /**
     * Update a branded QR code template
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id Template ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBranded(Request $request, $id)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $template = CustomQrTemplate::findOrFail($id);
        
        if (!$template->is_branded) {
            return redirect()->route('custom-qr.edit', $id);
        }
        
        // Validate form input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'main_color' => 'required|string',
            'secondary_color' => 'nullable|string',
            'background_color' => 'required|string',
            'brand_logo' => 'nullable|image|max:2048',
            'logo_size' => 'nullable|integer|min:30|max:150',
            'qr_shape' => 'required|in:square,rounded,circle,custom',
            'finder_pattern_style' => 'required|in:default,rounded,dot,custom',
            'show_finder_pattern' => 'nullable|in:0,1',
        ]);
        
        // Process the brand logo if uploaded
        if ($request->hasFile('brand_logo')) {
            // Delete old logo if exists
            if ($template->logo_path) {
                Storage::delete($template->logo_path);
            }
            $logoPath = $request->file('brand_logo')->store('public/custom-qr/logos');
            $template->logo_path = $logoPath;
        }
        
        // Convert hex colors to RGB
        list($r, $g, $b) = sscanf($validated['main_color'], "#%02x%02x%02x");
        $mainColor = ['r' => $r, 'g' => $g, 'b' => $b];
        
        list($r, $g, $b) = sscanf($validated['background_color'], "#%02x%02x%02x");
        $bgColor = ['r' => $r, 'g' => $g, 'b' => $b];
        
        $secondaryColor = null;
        if (!empty($validated['secondary_color'])) {
            list($r, $g, $b) = sscanf($validated['secondary_color'], "#%02x%02x%02x");
            $secondaryColor = ['r' => $r, 'g' => $g, 'b' => $b];
        }
        
        // Update template
        $template->update([
            'name' => $validated['name'],
            'fg_color' => json_encode($mainColor),
            'bg_color' => json_encode($bgColor),
            'secondary_color' => $secondaryColor ? json_encode($secondaryColor) : null,
            'logo_size' => $validated['logo_size'] ?? $template->logo_size ?? 80,
            'shape' => $validated['qr_shape'],
            'finder_pattern_style' => $validated['finder_pattern_style'],
            'show_finder_pattern' => $request->input('show_finder_pattern', '1'),
            'is_branded' => true,
            'error_correction' => 'H', // Always use high error correction for branded QR codes
        ]);
        
        // Generate a new sample if needed
        // This could be implemented to create a preview sample to save
        
        return redirect()->route('custom-qr.index')->with('success', 'Branded QR template updated successfully');
    }
    
    /**
     * Edit an advanced branded QR code template
     * 
     * @param int $id Template ID
     * @return \Illuminate\View\View
     */
    public function editAdvancedBranded($id)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $template = CustomQrTemplate::findOrFail($id);
        
        // If it's not a branded QR code or not an advanced branded QR, redirect to appropriate editor
        if (!$template->is_branded) {
            return redirect()->route('custom-qr.edit', $id);
        }
        
        if (!$template->is_advanced_branded) {
            return redirect()->route('custom-qr.editBranded', $id);
        }
        
        return view('custom-qr.edit-advanced-branded', compact('template'));
    }
    
    /**
     * Update an advanced branded QR code template
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id Template ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAdvancedBranded(Request $request, $id)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        $template = CustomQrTemplate::findOrFail($id);
        
        if (!$template->is_branded || !$template->is_advanced_branded) {
            return redirect()->route('custom-qr.index')->with('error', 'Invalid template type');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand_logo' => 'nullable|image|max:2048',
            'primary_color' => 'required|string',
            'secondary_color' => 'required|string',
            'background_color' => 'required|string',
            'brand_theme' => 'required|in:corporate,playful,minimal,bold',
        ]);
        
        try {
            // Process the brand logo if uploaded
            if ($request->hasFile('brand_logo')) {
                // Delete old logo if exists
                if ($template->logo_path) {
                    Storage::delete($template->logo_path);
                }
                $logoPath = $request->file('brand_logo')->store('public/custom-qr/logos');
                $template->logo_path = $logoPath;
            }
            
            // Convert colors from hex to RGB
            list($r, $g, $b) = sscanf($validated['primary_color'], "#%02x%02x%02x");
            $primaryColor = ['r' => $r, 'g' => $g, 'b' => $b];
            
            list($r, $g, $b) = sscanf($validated['secondary_color'], "#%02x%02x%02x");
            $secondaryColor = ['r' => $r, 'g' => $g, 'b' => $b];
            
            list($r, $g, $b) = sscanf($validated['background_color'], "#%02x%02x%02x");
            $bgColor = ['r' => $r, 'g' => $g, 'b' => $b];
            
            // Update template
            $template->update([
                'name' => $validated['name'],
                'fg_color' => json_encode($primaryColor),
                'bg_color' => json_encode($bgColor),
                'secondary_color' => json_encode($secondaryColor),
                'shape' => 'custom',
                'finder_pattern_style' => 'custom',
                'show_finder_pattern' => true,
                'error_correction' => 'H', // Always high for complex designs
                'is_branded' => true,
                'is_advanced_branded' => true,
                'brand_theme' => $validated['brand_theme'],
            ]);
            
            // Generate a new sample QR using the updated template
            if ($template->logo_path) {
                $logoFullPath = storage_path('app/' . $template->logo_path);
                
                // Generate the base QR code
                $sampleQrData = 'https://example.com/branded-sample-' . $template->id;
                $writer = new PngWriter();
                $qrCode = QrCode::create($sampleQrData)
                    ->setEncoding(new Encoding('UTF-8'))
                    ->setErrorCorrectionLevel(ErrorCorrectionLevel::from('high'))
                    ->setSize(300)
                    ->setMargin(10);
                    
                // Generate the basic QR
                $result = $writer->write($qrCode);
                
                // Create branded QR for sample
                $image = Image::make($result->getString());
                $logoImage = Image::make($logoFullPath);
                
                // Process according to selected theme
                $themeResult = $this->createBrandedQrByTheme(
                    $image, 
                    $logoImage, 
                    $validated['brand_theme'], 
                    $validated['primary_color'],
                    $validated['secondary_color'],
                    $validated['background_color']
                );
                
                // Save the branded QR sample
                $samplePath = 'public/custom-qr/samples/' . $template->id . '.png';
                
                // Delete existing sample if it exists
                if ($template->sample_qr_path) {
                    Storage::delete($template->sample_qr_path);
                }
                
                Storage::put($samplePath, (string)$themeResult->encode('png'));
                
                // Update the template with the sample path
                $template->update([
                    'sample_qr_path' => $samplePath
                ]);
            }
            
            return redirect()->route('custom-qr.index')->with('success', 'Advanced branded QR template updated successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update advanced branded QR: ' . $e->getMessage());
        }
    }

    /**
     * Show the QR code styling page using JavaScript library
     * 
     * @return \Illuminate\View\View
     */
    public function styledQR()
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        return view('custom-qr.styled-qr');
    }

    /**
     * Show the unified QR code editor page
     * 
     * @return \Illuminate\View\View
     */
    public function unifiedQrEditor(Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        // Get all templates for reference
        $templates = CustomQrTemplate::all();
        
        // Check if a specific template is requested for editing
        $selectedTemplate = null;
        
        if ($request->route('id')) {
            // If we're using a route parameter (e.g., /custom-qr/{id}/edit)
            $id = $request->route('id');
            \Log::info('Loading template for editing via route parameter: ' . $id);
            $selectedTemplate = CustomQrTemplate::find($id);
            
            if (!$selectedTemplate) {
                \Log::warning('Template not found: ' . $id);
                return redirect()->route('custom-qr.index')->with('error', 'Template not found');
            }
            
            \Log::info('Loaded template for editing: ' . $selectedTemplate->id . ' - ' . $selectedTemplate->name);
            
            // If we have a template with logo, prepare the logo URL
            if ($selectedTemplate->logo_path && !$this->isLogoInSettings($selectedTemplate)) {
                \Log::info('Template has a logo path: ' . $selectedTemplate->logo_path);
                // Make sure logos from storage are accessible via settings
                $this->appendLogoToSettings($selectedTemplate);
            }
        } elseif ($request->has('template')) {
            // If using a query parameter (e.g., ?template=1)
            \Log::info('Loading template for editing via query parameter: ' . $request->template);
            $selectedTemplate = CustomQrTemplate::find($request->template);
            
            if (!$selectedTemplate) {
                \Log::warning('Template not found: ' . $request->template);
            } else {
                \Log::info('Loaded template for editing: ' . $selectedTemplate->id . ' - ' . $selectedTemplate->name);
                
                // If we have a template with logo, prepare the logo URL
                if ($selectedTemplate->logo_path && !$this->isLogoInSettings($selectedTemplate)) {
                    \Log::info('Template has a logo path: ' . $selectedTemplate->logo_path);
                    // Make sure logos from storage are accessible via settings
                    $this->appendLogoToSettings($selectedTemplate);
                }
            }
        }
        
        return view('custom-qr.unified-qr-editor', compact('templates', 'selectedTemplate'));
    }
    
    /**
     * Check if logo is already in settings JSON
     */
    private function isLogoInSettings($template)
    {
        if (!$template->settings_json) {
            return false;
        }
        
        $settings = json_decode($template->settings_json, true);
        return $settings && isset($settings['image']) && !empty($settings['image']);
    }
    
    /**
     * Append logo path to settings JSON
     */
    private function appendLogoToSettings($template)
    {
        try {
            // Skip if no logo path
            if (!$template->logo_path) {
                return;
            }
            
            // Create settings array if not exists
            $settings = $template->settings_json ? json_decode($template->settings_json, true) : [];
            if (!$settings) {
                $settings = [];
            }
            
            // Generate public URL for logo
            $logoPath = str_replace('public/', '', $template->logo_path);
            $logoUrl = Storage::url($logoPath);
            
            \Log::info('Appending logo URL to settings: ' . $logoUrl);
            
            // Add logo to settings
            $settings['image'] = url($logoUrl);
            
            // Save updated settings
            $template->settings_json = json_encode($settings);
            $template->save();
            
            \Log::info('Updated settings_json with logo for template #' . $template->id);
        } catch (\Exception $e) {
            \Log::error('Error appending logo to settings: ' . $e->getMessage());
        }
    }

    /**
     * Save a template from the unified editor
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveTemplateFromEditor(Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return response()->json(['error' => 'Custom QR feature is disabled'], 403);
        }
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'qr_settings' => 'required|json',
                'template_id' => 'nullable',
                'sample_image' => 'nullable|string',
            ]);
            
            // Debug template ID value
            \Log::info('Template ID received: ' . $request->input('template_id'));
            
            // Decode QR settings
            $qrSettings = json_decode($validated['qr_settings'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('Invalid JSON in QR settings: ' . json_last_error_msg());
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR settings JSON: ' . json_last_error_msg()
                ], 400);
            }
            
            // Try to extract colors from QR settings
            $fgColor = ['r' => 0, 'g' => 0, 'b' => 0]; // Default black
            $bgColor = ['r' => 255, 'g' => 255, 'b' => 255]; // Default white
            
            // Extract foreground color if available
            if (isset($qrSettings['dotsOptions']['color'])) {
                $hexColor = $qrSettings['dotsOptions']['color'];
                if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $hexColor, $matches)) {
                    $fgColor = [
                        'r' => hexdec($matches[1]),
                        'g' => hexdec($matches[2]),
                        'b' => hexdec($matches[3])
                    ];
                    \Log::info("Extracted foreground color from settings: " . $hexColor);
                }
            } elseif (isset($qrSettings['dotsOptions']['gradient']) && isset($qrSettings['dotsOptions']['gradient']['colorStops'][0]['color'])) {
                // Try to get from gradient
                $hexColor = $qrSettings['dotsOptions']['gradient']['colorStops'][0]['color'];
                if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $hexColor, $matches)) {
                    $fgColor = [
                        'r' => hexdec($matches[1]),
                        'g' => hexdec($matches[2]),
                        'b' => hexdec($matches[3])
                    ];
                    \Log::info("Extracted foreground color from gradient: " . $hexColor);
                }
            }
            
            // Extract background color if available
            if (isset($qrSettings['backgroundOptions']['color'])) {
                $hexColor = $qrSettings['backgroundOptions']['color'];
                if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $hexColor, $matches)) {
                    $bgColor = [
                        'r' => hexdec($matches[1]),
                        'g' => hexdec($matches[2]),
                        'b' => hexdec($matches[3])
                    ];
                    \Log::info("Extracted background color from settings: " . $hexColor);
                }
            } elseif (isset($qrSettings['backgroundOptions']['gradient']) && isset($qrSettings['backgroundOptions']['gradient']['colorStops'][0]['color'])) {
                // Try to get from gradient
                $hexColor = $qrSettings['backgroundOptions']['gradient']['colorStops'][0]['color'];
                if (preg_match('/#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})/i', $hexColor, $matches)) {
                    $bgColor = [
                        'r' => hexdec($matches[1]),
                        'g' => hexdec($matches[2]),
                        'b' => hexdec($matches[3])
                    ];
                    \Log::info("Extracted background color from gradient: " . $hexColor);
                }
            }
            
            // Convert sample image if provided
            $samplePath = null;
            if (!empty($validated['sample_image'])) {
                try {
                    // Remove header from base64 image
                    $imageData = $validated['sample_image'];
                    if (strpos($imageData, ';base64,') !== false) {
                        $imageData = explode(';base64,', $imageData)[1];
                    }
                    
                    $samplePath = 'custom-qr/samples/' . uniqid() . '.png';
                    $storageResult = Storage::put('public/' . $samplePath, base64_decode($imageData));
                    
                    if ($storageResult) {
                        \Log::info('Successfully saved sample image to: ' . $samplePath);
                    } else {
                        \Log::error('Failed to save sample image to storage');
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to save sample image: ' . $e->getMessage());
                    // Continue without sample image
                }
            }
            
            // Process logo from QR settings if present
            $logoPath = null;
            $logoSize = 60; // Default size
            
            if (isset($qrSettings['image']) && !empty($qrSettings['image'])) {
                try {
                    // Check if it's a data URL (base64) image
                    if (strpos($qrSettings['image'], 'data:image') === 0) {
                        \Log::info('Processing base64 logo image from settings');
                        
                        // Extract the image data from base64
                        $imageData = $qrSettings['image'];
                        $base64Data = null;
                        
                        // Handle different base64 formats
                        if (strpos($imageData, ';base64,') !== false) {
                            // Get the image type
                            $type = explode('data:image/', $imageData)[1];
                            $type = explode(';', $type)[0]; // Get image type (png, jpeg, etc)
                            
                            // Validate image type
                            $allowedTypes = ['png', 'jpeg', 'jpg', 'gif', 'svg+xml'];
                            if (!in_array($type, $allowedTypes)) {
                                throw new \Exception("Invalid image format: $type. Allowed formats: png, jpeg, jpg, gif, svg");
                            }
                            
                            // Get the base64 content
                            $base64Data = explode(';base64,', $imageData)[1];
                            
                            // Validate image data
                            $decodedImage = base64_decode($base64Data);
                            if (!$decodedImage) {
                                throw new \Exception("Invalid image data");
                            }
                            
                            // Check file size (max 2MB)
                            if (strlen($decodedImage) > 2 * 1024 * 1024) {
                                throw new \Exception("Image file size exceeds maximum allowed (2MB)");
                            }
                            
                            // Fix file extension for SVG
                            $fileExtension = ($type === 'svg+xml') ? 'svg' : $type;
                            
                            // Save the logo image
                            $logoFilename = 'logo_' . uniqid() . '.' . $fileExtension;
                            $logoPath = 'public/custom-qr/logos/' . $logoFilename;
                            
                            // Ensure the directory exists
                            Storage::makeDirectory('public/custom-qr/logos');
                            
                            $logoResult = Storage::put($logoPath, $decodedImage);
                            
                            if ($logoResult) {
                                \Log::info('Successfully saved logo image to: ' . $logoPath);
                                
                                // Update QR settings to use the stored image path
                                $publicUrl = Storage::url(str_replace('public/', '', $logoPath));
                                $qrSettings['image'] = url($publicUrl);
                                
                                // Update the validated QR settings in JSON format
                                $validated['qr_settings'] = json_encode($qrSettings);
                                
                                // Get logo size from settings if available
                                if (isset($qrSettings['imageOptions']['imageSize'])) {
                                    $logoSize = (int)($qrSettings['imageOptions']['imageSize'] * 150); // Convert percentage to pixels
                                    \Log::info('Using logo size from settings: ' . $logoSize);
                                }
                            } else {
                                throw new \Exception('Failed to save logo image to storage');
                            }
                        } else {
                            throw new \Exception('Base64 image format not recognized: ' . substr($imageData, 0, 30) . '...');
                        }
                    }
                                            // If it's a file path from storage, keep it as is
                        elseif (strpos($qrSettings['image'], '/storage/') !== false || 
                                strpos($qrSettings['image'], env('APP_URL')) !== false) {
                            // If updating an existing template, check if we should keep the existing logo
                            if (isset($validated['template_id']) && $validated['template_id'] && 
                                $validated['template_id'] !== 'null' && $validated['template_id'] !== '0') {
                                // Try to find the template
                                $existingTemplate = CustomQrTemplate::find($validated['template_id']);
                                if ($existingTemplate && $existingTemplate->logo_path) {
                                    // Keep the existing logo path
                                    $logoPath = $existingTemplate->logo_path;
                                    \Log::info('Using existing template logo: ' . $logoPath);
                                    
                                    // Verify the file exists
                                    if (!Storage::exists($logoPath)) {
                                        \Log::warning('Existing logo file does not exist: ' . $logoPath);
                                    }
                                } else {
                                    // Try to extract the path from the URL
                                    $logoPath = str_replace('/storage/', 'public/', $qrSettings['image']);
                                    $logoPath = preg_replace('/^' . preg_quote(url('/'), '/') . '/', '', $logoPath);
                                    \Log::info('Extracted logo path from URL: ' . $logoPath);
                                    
                                    // Verify the file exists
                                    if (!Storage::exists($logoPath)) {
                                        \Log::warning('Logo file does not exist at extracted path: ' . $logoPath);
                                    }
                                }
                            } else {
                                // Try to extract the path from the URL
                                $logoPath = str_replace('/storage/', 'public/', $qrSettings['image']);
                                $logoPath = preg_replace('/^' . preg_quote(url('/'), '/') . '/', '', $logoPath);
                                \Log::info('Using existing logo path: ' . $logoPath);
                                
                                // Verify the file exists
                                if (!Storage::exists($logoPath)) {
                                    \Log::warning('Logo file does not exist: ' . $logoPath);
                                                                }
                            }
                        } else if (strpos($qrSettings['image'], 'blob:') === 0) {
                            // This is a local blob URL, can't be processed on the server
                            // We'll handle this in the frontend by converting to base64
                            \Log::warning('Blob URL detected in image but no file was uploaded: ' . substr($qrSettings['image'], 0, 30));
                            // Keep existing logo if this is an update
                            if (isset($validated['template_id']) && $validated['template_id'] && 
                                $validated['template_id'] !== 'null' && $validated['template_id'] !== '0') {
                                $existingTemplate = CustomQrTemplate::find($validated['template_id']);
                                if ($existingTemplate && $existingTemplate->logo_path) {
                                    $logoPath = $existingTemplate->logo_path;
                                    \Log::info('Keeping existing logo path for blob URL: ' . $logoPath);
                                }
                            }
                        } else {
                            throw new \Exception('Unrecognized image format in settings: ' . substr($qrSettings['image'], 0, 30) . '...');
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to process logo image: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                    // We'll continue without the logo, but return an error message to the client
                    return response()->json([
                        'success' => false,
                        'message' => 'Error processing logo: ' . $e->getMessage(),
                    ], 400);
                }
            }
            
            // Determine if this is an update or a new template
            $isUpdate = false;
            $template = null;
            
            // Process template_id - handle different formats (null, 'null', or numeric)
            $templateId = null;
            if (isset($validated['template_id'])) {
                if ($validated['template_id'] === null || $validated['template_id'] === 'null' || $validated['template_id'] === '' || $validated['template_id'] === 0 || $validated['template_id'] === '0') {
                    \Log::info('Template ID is null or equivalent, creating new template');
                    $isUpdate = false;
                } else {
                    $templateId = (int)$validated['template_id'];
                    \Log::info('Looking for template with ID: ' . $templateId);
                    try {
                        $template = CustomQrTemplate::find($templateId);
                        if ($template) {
                            $isUpdate = true;
                            \Log::info('Found existing template with ID: ' . $template->id);
                        } else {
                            \Log::warning('Could not find template with ID: ' . $templateId . '. Creating new template.');
                            $isUpdate = false;
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Error finding template with ID: ' . $templateId . '. ' . $e->getMessage());
                        $isUpdate = false;
                    }
                }
            }
            
            $data = [
                'name' => $validated['name'],
                'settings_json' => $validated['qr_settings'],
                'fg_color' => json_encode($fgColor),
                'bg_color' => json_encode($bgColor),
                'is_advanced_branded' => true,
                'error_correction' => 'H',
            ];
            
            // Add logo path if available
            if ($logoPath) {
                $data['logo_path'] = $logoPath;
                $data['logo_size'] = $logoSize;
            }
            
            // Add sample path if available
            if ($samplePath) {
                $data['sample_qr_path'] = $samplePath;
            }
            
            if ($isUpdate && $template) {
                // If updating and there was a previous logo but now there's a new one, delete the old logo
                if ($logoPath && $template->logo_path && $template->logo_path !== $logoPath) {
                    try {
                        Storage::delete($template->logo_path);
                        \Log::info('Deleted old logo: ' . $template->logo_path);
                    } catch (\Exception $e) {
                        \Log::error('Error deleting old logo: ' . $e->getMessage());
                    }
                }
                
                // Update existing template
                $updateData = [
                    'name' => $validated['name'],
                    'settings_json' => $validated['qr_settings'],
                    'sample_qr_path' => $samplePath ?? $template->sample_qr_path,
                    'fg_color' => json_encode($fgColor),
                    'bg_color' => json_encode($bgColor),
                    'error_correction' => 'H', // Always use High error correction for templates with advanced options
                    'is_advanced_branded' => true,
                ];
                
                // Only set logo_path if a new logo was provided
                if ($logoPath) {
                    $updateData['logo_path'] = $logoPath;
                    $updateData['logo_size'] = $logoSize;
                } 
                // Only remove logo_path if image was explicitly removed in the settings
                else if (!isset($qrSettings['image']) || $qrSettings['image'] === '') {
                    $updateData['logo_path'] = null;
                    // Also remove any image from the settings JSON
                    $qrSettingsObj = json_decode($validated['qr_settings'], true);
                    if (isset($qrSettingsObj['image'])) {
                        unset($qrSettingsObj['image']);
                        unset($qrSettingsObj['imageOptions']);
                        $updateData['settings_json'] = json_encode($qrSettingsObj);
                    }
                }
                // If no new logo was provided but settings contain an image URL, keep the existing logo
                else if (isset($qrSettings['image']) && !empty($qrSettings['image']) && $template->logo_path) {
                    \Log::info('Keeping existing logo: ' . $template->logo_path);
                    // No need to update logo_path as we're keeping the existing one
                }
                
                $template->update($updateData);
                
                \Log::info('Updated template: ' . $template->id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Template updated successfully',
                    'template' => $template,
                ]);
            } else {
                // Create new template with explicit logo_path setting
                $createData = [
                    'name' => $validated['name'],
                    'settings_json' => $validated['qr_settings'],
                    'sample_qr_path' => $samplePath,
                    'error_correction' => 'H', // Always use High error correction for templates with advanced options
                    'fg_color' => json_encode($fgColor),
                    'bg_color' => json_encode($bgColor),
                    'is_advanced_branded' => true,
                    'logo_path' => $logoPath, // This will be null if no logo was provided
                ];
                
                if ($logoPath) {
                    $createData['logo_size'] = $logoSize;
                }
                
                $template = CustomQrTemplate::create($createData);
                
                \Log::info('Created new template: ' . $template->id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Template created successfully',
                    'template' => $template,
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in saveTemplateFromEditor: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', array_map(function($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in saveTemplateFromEditor: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save template: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Set a template as the default template
     * 
     * @param int $id Template ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setAsDefault($id)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        try {
            // Reset all templates to non-default
            CustomQrTemplate::where('is_default', true)->update(['is_default' => false]);
            
            // Set the selected template as default
            $template = CustomQrTemplate::findOrFail($id);
            $template->update(['is_default' => true]);
            
            return redirect()->route('custom-qr.index')->with('success', 'Template "' . $template->name . '" set as default');
        } catch (\Exception $e) {
            return redirect()->route('custom-qr.index')->with('error', 'Failed to set template as default: ' . $e->getMessage());
        }
    }

    /**
     * API preview for templates - returns JSON with QR data in same format as JavaScript library
     */
    public function apiPreview($id, Request $request)
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return response()->json(['error' => 'Custom QR feature is disabled'], 403);
        }
        
        try {
            $template = CustomQrTemplate::findOrFail($id);
            $sampleData = $request->input('data', 'https://example.com/sample-qr-code');
            
            \Log::info('API Preview for QR template: ' . $id);
            
            // Make sure template settings are up to date with logo
            if ($template->logo_path && !$this->isLogoInSettings($template)) {
                $this->appendLogoToSettings($template);
            }
            
            // Get the settings
            $settings = null;
            if ($template->settings_json) {
                $settings = json_decode($template->settings_json, true);
                // Add the data to the settings
                $settings['data'] = $sampleData;
                
                // Ensure width, height, and margin have valid values
                $settings['width'] = isset($settings['width']) && is_numeric($settings['width']) && $settings['width'] > 0 
                    ? (int)$settings['width'] : 300;
                $settings['height'] = isset($settings['height']) && is_numeric($settings['height']) && $settings['height'] > 0 
                    ? (int)$settings['height'] : 300;
                $settings['margin'] = isset($settings['margin']) && is_numeric($settings['margin']) 
                    ? (int)$settings['margin'] : 10;
                
                // Log the dimension values for debugging
                \Log::info('QR dimensions: ' . $settings['width'] . 'x' . $settings['height'] . ', margin: ' . $settings['margin']);
            } else {
                // Create default settings
                $settings = [
                    'width' => 300,
                    'height' => 300,
                    'type' => 'svg',
                    'data' => $sampleData,
                    'margin' => 10,
                    'qrOptions' => [
                        'typeNumber' => 0,
                        'mode' => 'Byte',
                        'errorCorrectionLevel' => 'H'
                    ],
                    'dotsOptions' => [
                        'color' => '#000000',
                        'type' => 'square'
                    ],
                    'backgroundOptions' => [
                        'color' => '#ffffff'
                    ]
                ];
                
                // Add logo if present
                if ($template->logo_path) {
                    $logoUrl = Storage::url(str_replace('public/', '', $template->logo_path));
                    $settings['image'] = url($logoUrl);
                    $settings['imageOptions'] = [
                        'hideBackgroundDots' => true,
                        'imageSize' => 0.4,
                        'margin' => 5,
                        'crossOrigin' => 'anonymous'
                    ];
                }
            }
            
            // Generate QR in SVG format for web preview
            // Create QR code with appropriate error correction level
            $qrCode = \Endroid\QrCode\QrCode::create($sampleData)
                ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
                ->setSize(isset($settings['width']) ? (int)$settings['width'] : 300)
                ->setMargin(isset($settings['margin']) ? (int)$settings['margin'] : 10);
            
            // Set foreground and background colors
            $fgColor = json_decode($template->fg_color, true);
            $bgColor = json_decode($template->bg_color, true);
            
            if ($fgColor && is_array($fgColor)) {
                $qrCode->setForegroundColor(new \Endroid\QrCode\Color\Color($fgColor['r'], $fgColor['g'], $fgColor['b']));
            }
            
            if ($bgColor && is_array($bgColor)) {
                $qrCode->setBackgroundColor(new \Endroid\QrCode\Color\Color($bgColor['r'], $bgColor['g'], $bgColor['b']));
            }
            
            // Create SVG writer for web preview
            $writer = new \Endroid\QrCode\Writer\SvgWriter();
            
            // Add logo if template has one
            $result = null;
            if ($template->logo_path && \Illuminate\Support\Facades\Storage::exists($template->logo_path)) {
                $logoPath = \Illuminate\Support\Facades\Storage::path($template->logo_path);
                $logo = \Endroid\QrCode\Logo\Logo::create($logoPath)
                    ->setResizeToWidth(isset($template->logo_size) ? (int)$template->logo_size : 80);
                
                $result = $writer->write($qrCode, $logo);
            } else {
                $result = $writer->write($qrCode);
            }
            
            // Build the response with settings and preview
            $response = [
                'success' => true,
                'template' => [
                    'id' => $template->id,
                    'name' => $template->name,
                    'settings' => $settings
                ],
                'preview' => [
                    'svg' => base64_encode($result->getString()),
                    'mime' => $result->getMimeType()
                ]
            ];
            
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in API preview: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Regenerate all QR codes using the default template
     */
    public function regenerateAllQrCodes()
    {
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        try {
            // Get default template
            $defaultTemplate = CustomQrTemplate::where('is_default', true)->first();
            
            if (!$defaultTemplate) {
                return redirect()->route('custom-qr.index')->with('error', 'No default template found');
            }
            
            // Get all invitations
            $invitations = \App\Models\Invitation::all();
            $count = 0;
            
            foreach ($invitations as $invitation) {
                $qrData = $invitation->qrcode_invitation;
                
                // Create QR code with appropriate error correction level
                $qrCode = \Endroid\QrCode\QrCode::create($qrData)
                    ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                    ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
                    ->setSize(300)
                    ->setMargin(10);
                
                // Set foreground and background colors
                $fgColor = json_decode($defaultTemplate->fg_color, true);
                $bgColor = json_decode($defaultTemplate->bg_color, true);
                
                if ($fgColor && is_array($fgColor)) {
                    $qrCode->setForegroundColor(new \Endroid\QrCode\Color\Color($fgColor['r'], $fgColor['g'], $fgColor['b']));
                }
                
                if ($bgColor && is_array($bgColor)) {
                    $qrCode->setBackgroundColor(new \Endroid\QrCode\Color\Color($bgColor['r'], $bgColor['g'], $bgColor['b']));
                }
                
                // Create writer
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                
                // Add logo if template has one
                $result = null;
                if ($defaultTemplate->logo_path && \Illuminate\Support\Facades\Storage::exists($defaultTemplate->logo_path)) {
                    $logoPath = \Illuminate\Support\Facades\Storage::path($defaultTemplate->logo_path);
                    $logo = \Endroid\QrCode\Logo\Logo::create($logoPath)
                        ->setResizeToWidth(isset($defaultTemplate->logo_size) ? (int)$defaultTemplate->logo_size : 80);
                    
                    $result = $writer->write($qrCode, $logo);
                } else {
                    $result = $writer->write($qrCode);
                }
                
                // Save the QR code to storage and public directory
                $qrImagePath = 'public/img/qrCode/' . $invitation->qrcode_invitation . '.png';
                \Illuminate\Support\Facades\Storage::put($qrImagePath, $result->getString());
                
                // Also save to public path for backward compatibility
                file_put_contents(public_path('/img/qrCode/' . $invitation->qrcode_invitation . '.png'), $result->getString());
                
                // Update invitation with custom QR path
                $invitation->update([
                    'custom_qr_template_id' => $defaultTemplate->id,
                    'custom_qr_path' => $qrImagePath
                ]);
                
                $count++;
            }
            
            return redirect()->route('custom-qr.index')->with('success', "Successfully regenerated $count QR codes using the default template.");
        } catch (\Exception $e) {
            \Log::error('Error in regenerateAllQrCodes: ' . $e->getMessage());
            return redirect()->route('custom-qr.index')->with('error', 'Failed to regenerate QR codes: ' . $e->getMessage());
        }
    }

    public function applyToAllGuests($templateId)
    {
        // Make sure the custom QR feature is enabled
        if (!isset(mySetting()->enable_custom_qr) || mySetting()->enable_custom_qr != 1) {
            return redirect('dashboard')->with('error', 'Custom QR feature is disabled');
        }
        
        try {
            // Find the template
            $defaultTemplate = CustomQrTemplate::findOrFail($templateId);
            
            // Set it as the default template
            CustomQrTemplate::where('id', '!=', $templateId)->update(['is_default' => false]);
            $defaultTemplate->update(['is_default' => true]);
            
            // Get all invitations
            $invitations = Invitation::all();
            
            $processed = 0;
            $errors = 0;
            
            foreach ($invitations as $invitation) {
                $qrData = $invitation->qrcode_invitation;
                
                // Create QR code with appropriate error correction level
                $qrCode = \Endroid\QrCode\QrCode::create($qrData)
                    ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                    ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
                    ->setSize(300)
                    ->setMargin(10);
                
                // Set foreground and background colors
                $fgColor = json_decode($defaultTemplate->fg_color, true);
                $bgColor = json_decode($defaultTemplate->bg_color, true);
                
                if ($fgColor && is_array($fgColor)) {
                    $qrCode->setForegroundColor(new \Endroid\QrCode\Color\Color($fgColor['r'], $fgColor['g'], $fgColor['b']));
                }
                
                if ($bgColor && is_array($bgColor)) {
                    $qrCode->setBackgroundColor(new \Endroid\QrCode\Color\Color($bgColor['r'], $bgColor['g'], $bgColor['b']));
                }
                
                // Create writer
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                
                // Add logo if template has one
                $result = null;
                if ($defaultTemplate->logo_path && \Illuminate\Support\Facades\Storage::exists($defaultTemplate->logo_path)) {
                    $logoPath = \Illuminate\Support\Facades\Storage::path($defaultTemplate->logo_path);
                    $logo = \Endroid\QrCode\Logo\Logo::create($logoPath)
                        ->setResizeToWidth(isset($defaultTemplate->logo_size) ? (int)$defaultTemplate->logo_size : 80);
                    
                    $result = $writer->write($qrCode, $logo);
                } else {
                    $result = $writer->write($qrCode);
                }
                
                try {
                    // Define paths
                    $publicPath = '/img/qrCode/' . $qrData . '.png';
                    $storagePath = 'public/img/qrCode/' . $qrData . '.png';
                    
                    // Save to both public and storage
                    file_put_contents(public_path($publicPath), $result->getString());
                    \Illuminate\Support\Facades\Storage::put($storagePath, $result->getString());
                    
                    // Update invitation with template info
                    $invitation->update([
                        'custom_qr_template_id' => $defaultTemplate->id,
                        'custom_qr_path' => $storagePath
                    ]);
                    
                    $processed++;
                } catch (\Exception $e) {
                    \Log::error("Error generating QR for invitation " . $invitation->id_invitation . ": " . $e->getMessage());
                    $errors++;
                }
            }
            
            return redirect()->route('custom-qr.index')->with('success', "Template set as default and applied to $processed guests" . ($errors > 0 ? " ($errors errors)" : ""));
            
        } catch (\Exception $e) {
            \Log::error("Error in applyToAllGuests: " . $e->getMessage());
            return redirect()->route('custom-qr.index')->with('error', 'Error applying template to all guests: ' . $e->getMessage());
        }
    }
} 