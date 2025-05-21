<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Invitation;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvitationController extends Controller
{

    //  Link for Guest access
    public function linkGuest($qrcode, Request $request)
    {
        // Generate QR code
        \Log::info("Generating QR code for link guest page: {$qrcode}");
        $this->qrcodeGenerator($qrcode, true);

        $invt = Invitation::where('qrcode_invitation', $qrcode)->first();
        $event = Event::where('id_event', 1)->first();
        if($invt) {
            return view('link-guest.index', compact('invt', 'event'));
        } else {
            return view('link-guest.notFound');
        }
    }

    /**
     * Get standardized QR code path - using public directory only
     * 
     * @param string $code QR code
     * @param bool $fullPath Return full system path if true, relative path if false
     * @return string Path to QR code
     */
    private function getQrCodePath($code, $fullPath = false)
    {
        $relativePath = 'img/qrCode/' . $code . '.png';
        return $fullPath ? public_path($relativePath) : $relativePath;
    }
    
    /**
     * Check if QR code file exists
     * 
     * @param string $code QR code
     * @return bool Whether file exists
     */
    private function qrCodeFileExists($code)
    {
        $path = public_path('img/qrCode/' . $code . '.png');
        $exists = File::exists($path);
        if ($exists) {
            \Log::info("QR code file for {$code} exists at {$path}");
            // Check file integrity
            $fileSize = File::size($path);
            \Log::info("QR code file size: {$fileSize} bytes");
            
            if ($fileSize < 100) {
                \Log::warning("QR code file for {$code} is suspiciously small: {$fileSize} bytes");
                return false;
            }
        } else {
            \Log::info("QR code file for {$code} does not exist at {$path}");
        }
        return $exists;
    }

    /**
     * Download QR code
     * 
     * @param string $code QR code to download
     * @return \Illuminate\Http\Response
     */
    public function downloadQrCode($code)
    {
        $invitation = Invitation::where('qrcode_invitation', $code)->first();
        
        if (!$invitation) {
            \Log::warning("No invitation found for code: $code for download");
            abort(404, 'Invitation not found');
        }
        
        $qrOutputPath = public_path('img/qrCode/' . $code . '.png');
        
        // Force regenerate QR to ensure it's up-to-date
        \Log::info("Regenerating QR code for download: {$code}");
        $this->qrcodeGenerator($code, true);
        
        if (!file_exists($qrOutputPath)) {
            \Log::error("Failed to find QR code file after generation for: {$code}");
            abort(404, 'QR Code file not found');
        }
        
        \Log::info("Downloading QR code from: {$qrOutputPath}");
        return response()->download($qrOutputPath, $code . '.png', [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT'
        ]);
    }

    //  Link for Guest send email
    public function linkGuestEmail($qrcode, $trackingCode = null)
    {
        $invt = Invitation::where('qrcode_invitation', $qrcode)->first();
        $event = Event::where('id_event', 1)->first();
        return view('link-guest.sendMail', compact('invt', 'event', 'trackingCode'));
    }

    private function checkUniq($qrcode)
    {
        $cek = Invitation::where('qrcode_invitation', $qrcode)->get();
        return $cek->count() > 0 ? TRUE : FALSE;
    }
    
    private function generateCode()
    {
        // Generate a 6-character QR code with mixed case letters and numbers
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $qrcode = '';
        
        // Generate a 6-character random string
        for ($i = 0; $i < 6; $i++) {
            $qrcode .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        // Ensure uniqueness by recursively checking if the code already exists
        if ($this->checkUniq($qrcode)) {
            return $this->generateCode();
        }
        
        return $qrcode;
    }

    public function qrcodeGenerator($code, $forceRegenerate = false)
    {
        // Check if QR code already exists and we're not forcing regeneration
        if (!$forceRegenerate && $this->qrCodeFileExists($code)) {
            \Log::info("Skipping QR regeneration for {$code} - file already exists");
            return;
        }
        
        \Log::info("Generating QR code for: {$code}, force: " . ($forceRegenerate ? 'yes' : 'no'));
        
        // Ensure directory exists
        File::ensureDirectoryExists(public_path('img/qrCode'));
        
        // Get invitation record
        $invitation = Invitation::where('qrcode_invitation', $code)->first();
        if (!$invitation) {
            \Log::warning("No invitation found for code: $code");
        }
        
        // Define path
        $qrPath = $this->getQrCodePath($code);
        $fullPath = $this->getQrCodePath($code, true);
        
        // Generate basic QR code using Endroid
        $qrData = route('link-guest', ['qrcode' => $code]);
            
        // Create basic QR with Endroid
                    $qrCode = \Endroid\QrCode\QrCode::create($qrData)
                        ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                        ->setErrorCorrectionLevel(\Endroid\QrCode\ErrorCorrectionLevel::High)
                        ->setSize(300)
                        ->setMargin(10);
                    
        // Add black and white coloring
        $qrCode->setForegroundColor(new \Endroid\QrCode\Color\Color(0, 0, 0));
        $qrCode->setBackgroundColor(new \Endroid\QrCode\Color\Color(255, 255, 255));
                    
        // Write the QR code to file
                    $writer = new \Endroid\QrCode\Writer\PngWriter();
                        $result = $writer->write($qrCode);
                    
                    file_put_contents($fullPath, $result->getString());
                    
        // Update invitation if needed
                    if ($invitation) {
                        $invitation->update([
                            'image_qrcode_invitation' => '/' . $qrPath
                        ]);
        }
        
        \Log::info("QR code generated successfully for {$code}");
    }

    public function sendEmail() {
        $guestQrcode = request()->input('guestQrcode');
        $guestMail = request()->input('guestMail');
        $guestName = request()->input('guestName');
        
        // Track that email will be sent
        Log::info("Sending email to: {$guestMail}, Name: {$guestName}, QR Code: {$guestQrcode}");
        
        // Check if device is mobile
        $userAgent = request()->header('User-Agent');
        $isMobile = preg_match('/(android|iPhone|iPad|iPod|Windows Phone)/i', $userAgent);
        
        $qr = Invitation::where('qrcode_invitation', $guestQrcode)->first();
        $mail = new PHPMailer(true);
        $event = \App\Models\Setting::first();
        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = env("MAIL_HOST");                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = env("MAIL_USERNAME");                     //SMTP username
            $mail->Password   = env("MAIL_PASSWORD");                               //SMTP password
            $mail->SMTPSecure = env("MAIL_ENCRYPTION") == 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = env("MAIL_PORT");                       //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $mail->setFrom(env("MAIL_FROM_ADDRESS"), myEvent()->name_event);
            $mail->addAddress($guestMail, $guestName);    

            if ($event->image_bg_event != '' && $event->image_bg_status == 1) {
                $mail->AddEmbeddedImage(public_path('img/event/' . $event->image_bg_event), 'bg');
            }
            
            if ($event->image_event != '' && $event->image_top_status == 1) {
                $mail->AddEmbeddedImage(public_path('img/event/' . $event->image_event), 'imgtop');   
            }
            else {
                $mail->AddEmbeddedImage(public_path('asset/front/default.png'), 'imgtop');   
            }

            $mail->AddEmbeddedImage(public_path('img/qrCode/' . $guestQrcode . '.png'), 'qrcode');  
            
            // Add tracking code parameter (timestamp + email hash for security)
            $trackingCode = base64_encode(json_encode([
                'email' => $guestMail,
                'time' => time(),
                'code' => $guestQrcode,
                'hash' => md5($guestMail . $guestQrcode . env('APP_KEY'))
            ]));
       
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = myEvent()->type_event;
            
            // Pass tracking code to the view
            $mail->Body = $this->linkGuestEmail($guestQrcode, $trackingCode)->render();

            $mail->send();
            $mail->SmtpClose();

            Invitation::where('qrcode_invitation', $guestQrcode)->update([
                'send_email_invitation' => 1,
                'email_sent' => true,
                'email_read' => false,
                'email_bounced' => false,
                'tracking_code' => $trackingCode // Save tracking code for verification later
            ]);

            $status = "success";
            $message = "Berhasil mengirim email";
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            Invitation::where('qrcode_invitation', $guestQrcode)->update([
                'send_email_invitation' => 1,
                'email_sent' => true,
                'email_bounced' => true
            ]);
            
            $status = "error";
            $message = "Gagal mengirim ke email";
        }
        return redirect("invite")->with($status, $message);
    }
    
    public function sendWhatsapp() {
    }

    public function index()
    {
        $invitations = Invitation::orderBy('id_invitation', 'DESC')
                            ->orderBy('name_guest', 'ASC')
                            ->get();
                            
        // Add custom QR templates if the feature is enabled
        $customQrTemplates = [];
        if (isset(mySetting()->enable_custom_qr) && mySetting()->enable_custom_qr == 1) {
            $customQrTemplates = \App\Models\CustomQrTemplate::all();
        }
        
        return view('invitation.index', compact('invitations', 'customQrTemplates'));
    }

    public function create()
    {
        return view('invitation.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $qrcode = $this->generateCode();
        $this->qrcodeGenerator($qrcode);

        Invitation::create([
            "name_guest" => $request->name,
            "email_guest" => $request->email,
            "phone_guest" => $request->phone,
            "address_guest" => $request->address,
            "company_guest" => $request->company,
            "custom_message" => $request->custom_message,
            "created_by_guest" => "admin",
            "qrcode_invitation" => $qrcode,
            "table_number_invitation" => $request->table_number,
            "type_invitation" => $request->type,
            "information_invitation" => $request->information,
            "link_invitation" => '/invitation/' . $qrcode,
            "image_qrcode_invitation" => '/img/qrCode/' . $qrcode . ".png",
            "id_event" => 1,
        ]);

        return redirect('/invite')->with('success', "Berhasil menambah data");
    }

    public function edit($id)
    {
        $invitation = Invitation::where('id_invitation', $id)->first();
        return view('invitation.edit', compact('invitation'));
    }

    public function update(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'type' => 'required',
      ]);

      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $invitation = Invitation::findOrFail($id);

      $invitation->update([
        'name_guest' => $request->name,
        'email_guest' => $request->email,
        'phone_guest' => $request->phone,
        'address_guest' => $request->address,
        'company_guest' => $request->company,
        'custom_message' => $request->custom_message,
        'type_invitation' => $request->type,
        'table_number_invitation' => $request->table_number,
        'information_invitation' => $request->information,
      ]);

      return redirect('invite')->with('success', "Berhasil mengedit data");
    }

    public function delete(Request $request)
    {
        // Use the helper method for consistent file path
        $qrFilePath = $this->getQrCodePath($request->qrcode, true);
        if (file_exists($qrFilePath)) {
            unlink($qrFilePath);
        }
        
        if (file_exists(public_path('/img/scan/scan-in/' . $request->qrcode . ".jpeg"))) {
            unlink(public_path('/img/scan/scan-in/' . $request->qrcode . ".jpeg"));
        }   
        if (file_exists(public_path('/img/scan/scan-out/' . $request->qrcode . ".jpeg"))) {
            unlink(public_path('/img/scan/scan-out/' . $request->qrcode . ".jpeg"));
        }
        Invitation::where('id_invitation', $request->id_invitation)->delete();
        return redirect('invite')->with('success', "Berhasil menghapus data");
    }


    // Guest Register - now just direct invitation registration
    public function guestRegister()
    {
        return view('register.form');
    }

    public function guestRegisterProcess(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'company' => 'required',
      ]);

      if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
      }

      $qrcode = $this->generateCode();
      $this->qrcodeGenerator($qrcode);
      
      Invitation::create([
        "name_guest" => $request->name,
        "email_guest" => $request->email,
        "phone_guest" => $request->phone,
        "address_guest" => $request->address,
        "company_guest" => $request->company,
        "created_by_guest" => "register",
        "qrcode_invitation" => $qrcode,
        "type_invitation" => "reguler",
        "link_invitation" => '/invitation/' . $qrcode,
        "image_qrcode_invitation" => '/img/qrCode/' . $qrcode . ".png",
        "id_event" => 1,
      ]);

      return redirect('/invitation/' . $qrcode)->with("register-success", "Terima kasih telah melakukan registrasi. Silahkan download QR untuk ditunjukan saat acara.");
    }
}
