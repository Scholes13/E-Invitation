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

class InvitationController extends Controller
{

    //  Link for Guest access
    public function linkGuest($qrcode)
    {
        // Check if custom QR feature is enabled
        $customQrEnabled = isset(mySetting()->enable_custom_qr) && mySetting()->enable_custom_qr == 1;
        
        // Always regenerate QR code if custom QR is enabled and a default template exists
        if ($customQrEnabled) {
        $defaultTemplate = \App\Models\CustomQrTemplate::where('is_default', true)->first();
        if ($defaultTemplate) {
            $this->qrcodeGenerator($qrcode);
            }
        } 
        // When custom QR is disabled, always regenerate standard QR code to remove any custom styling
        else {
            $this->qrcodeGenerator($qrcode);
        }

        $invt = Invitation::where('qrcode_invitation', $qrcode)->first();
        $event = Event::where('id_event', 1)->first();
        if($invt) {
            return view('link-guest.index', compact('invt', 'event'));
        } else {
            return view('link-guest.notFound');
        }
        
    }
    public function downloadQrCode($code)
    {
        // Check if custom QR feature is enabled
        $customQrEnabled = isset(mySetting()->enable_custom_qr) && mySetting()->enable_custom_qr == 1;
        $invitation = Invitation::where('qrcode_invitation', $code)->first();
        
        // Only use custom QR path if feature is enabled
        if ($customQrEnabled && $invitation && $invitation->custom_qr_path) {
            // Convert storage path to file system path
            $path = storage_path('app/' . $invitation->custom_qr_path);
            
            // If file doesn't exist in storage, fall back to public path
            if (!file_exists($path)) {
                return response()->download(public_path('img/qrCode/'. $code. ".png"));
            }
            
            return response()->download($path, $code . '.png');
        }
        
        // Fallback to standard path
        return response()->download(public_path('img/qrCode/'. $code. ".png"));
    }

    //  Link for Guest send email
    public function linkGuestEmail($qrcode)
    {
        $invt = Invitation::where('qrcode_invitation', $qrcode)->first();
        $event = Event::where('id_event', 1)->first();
        return view('link-guest.sendMail', compact('invt', 'event'));
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

    public function qrcodeGenerator($code)
    {
        // Ensure directories exist
        File::ensureDirectoryExists(public_path('img/qrCode'));
        File::ensureDirectoryExists(storage_path('app/public/img/qrCode'));
        
        // Get invitation record
        $invitation = Invitation::where('qrcode_invitation', $code)->first();
        if (!$invitation) {
            \Log::warning("No invitation found for code: $code");
        }
        
        // Check if a default template exists AND custom QR feature is enabled
        $customQrEnabled = isset(mySetting()->enable_custom_qr) && mySetting()->enable_custom_qr == 1;
        $defaultTemplate = $customQrEnabled ? \App\Models\CustomQrTemplate::where('is_default', true)->first() : null;
        
        if ($customQrEnabled && $defaultTemplate) {
            // Generate QR with default template - use just the code, not the full URL
            $qrData = $code;
            
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
            if ($defaultTemplate->logo_path && Storage::exists($defaultTemplate->logo_path)) {
                $logoPath = Storage::path($defaultTemplate->logo_path);
                $logo = \Endroid\QrCode\Logo\Logo::create($logoPath)
                    ->setResizeToWidth(isset($defaultTemplate->logo_size) ? (int)$defaultTemplate->logo_size : 80);
                
                $result = $writer->write($qrCode, $logo);
            } else {
                $result = $writer->write($qrCode);
            }
            
            // Define paths
            $publicPath = '/img/qrCode/' . $code . '.png';
            $storagePath = 'public/img/qrCode/' . $code . '.png';
            
            // Save to both public and storage
            file_put_contents(public_path($publicPath), $result->getString());
            \Illuminate\Support\Facades\Storage::put($storagePath, $result->getString());
            
            // Update invitation with template info
            if ($invitation) {
                $invitation->update([
                    'custom_qr_template_id' => $defaultTemplate->id,
                    'custom_qr_path' => $storagePath
                ]);
                \Log::info("Updated invitation #$invitation->id_invitation with custom QR template #$defaultTemplate->id");
            }
            
            return; // Exit the method after generating custom QR
        }
        
        // Use standard QR generation if no default template or custom QR is disabled
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($code) // Use just the code, not the full URL
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->validateResult(false)
            ->build();
        $result->saveToFile(public_path('/img/qrCode/' . $code . '.png'));
    }

    public function sendEmail() {
        $event = Event::where('id_event', 1)->first();
        $mail = new PHPMailer(true);
        try {
            $guestQrcode    = $_GET['guestQrcode'];
            $guestName      = $_GET['guestName'];
            $guestMail      = $_GET['guestMail'];
            if($event->image_event != "") :
                $img = 'img/event/'.$event->image_event;
            else :
                $img = 'asset/front/default.png';
            endif;
            // set_time_limit(0); // remove a time limit if not in safe mode OR
            set_time_limit(180); // set the time limit to 120 seconds

            $mail->SMTPDebug  = SMTP::DEBUG_OFF;                        //Enable verbose debug output
            $mail->isSMTP();           
            $mail->Timeout    = 120;                                
            $mail->SMTPKeepAlive = true;
            $mail->Host       = env("MAIL_HOST");                       //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = env("MAIL_USERNAME");                   //SMTP username
            $mail->Password   = env("MAIL_PASSWORD");                   //SMTP password
            $mail->SMTPSecure = env("MAIL_ENCRYPTION");                 //Enable implicit TLS encryption
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
       
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = myEvent()->type_event;
            $mail->Body    = $this->linkGuestEmail($guestQrcode)->render();

            $mail->send();
            $mail->SmtpClose();

            Invitation::where('qrcode_invitation', $guestQrcode)->update([
                'send_email_invitation' => 1,
                'email_sent' => true,
                'email_read' => false,
                'email_bounced' => false
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
        if (file_exists(public_path('/img/qrCode/' . $request->qrcode . ".png"))) {
            unlink(public_path('/img/qrCode/' . $request->qrcode . ".png"));
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
