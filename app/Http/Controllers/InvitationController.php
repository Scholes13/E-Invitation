<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
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
use Illuminate\Support\Facades\Log;

class InvitationController extends Controller
{
    // Constant for the QR code path - use this everywhere
    private const QR_DIR = 'img/qrCode'; // consistently use lowercase

    // Link for Guest access
    public function linkGuest($qrcode)
    {
        // Check QR code with consistent path
        $qrPath = public_path(self::QR_DIR . '/' . $qrcode . '.png');

        if (!file_exists($qrPath)) {
            Log::info("QR code not found at: " . $qrPath . ". Generating a new one.");
            $this->qrcodeGenerator($qrcode);
        }

        $invt = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
            ->where('qrcode_invitation', $qrcode)->first();
        $event = Event::where('id_event', 1)->first();
        if ($invt) {
            return view('link-guest.index', compact('invt', 'event'));
        } else {
            return view('link-guest.notFound');
        }
    }

    public function downloadQrCode($code)
    {
        // Use consistent path constant
        return response()->download(public_path(self::QR_DIR . '/' . $code . '.png'));
    }

    // Link for Guest send email
    public function linkGuestEmail($qrcode)
    {
        $invt = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
            ->where('qrcode_invitation', $qrcode)->first();
        $event = Event::where('id_event', 1)->first();
        return view('link-guest.sendMail', compact('invt', 'event'));
    }

    public function getGuest(Request $request)
    {
        if ($request->ajax()) {
            $data = Guest::where('id_guest', $request->id_guest)->first();
            return response()->json([
                'status' => "success",
                'data'  => $data
            ]);
        }
    }

    private function checkUniq($qrcode)
    {
        $cek = Invitation::where('qrcode_invitation', $qrcode)->get();
        return $cek->count() > 0 ? TRUE : FALSE;
    }
    private function generateCode()
    {
        $qrcode = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); // Generate 4-digit number
        if ($this->checkUniq($qrcode)) {
            return $this->generateCode(); // Regenerate if not unique
        }
        return $qrcode;
    }

    public function qrcodeGenerator($code)
    {
        try {
            // Log debug info
            Log::info("Generating QR code for: " . $code);

            // Ensure directory exists and use consistent path
            $qrDir = self::QR_DIR;
            $fullPath = base_path(self::QR_DIR);

            // Ensure the directory exists
            if (!is_dir($fullPath)) {
                Log::info("Creating directory: " . $fullPath);
                mkdir($fullPath, 0755, true);
            }

            // Ensure APP_URL contains https if the site uses HTTPS
            $appUrl = env('APP_URL');
            if (empty($appUrl)) {
                // Fallback to dynamic URL if APP_URL is empty
                $appUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
                Log::warning("APP_URL empty, using dynamic URL: " . $appUrl);
            }

            $url = $appUrl . '/invitation/' . $code;
            Log::info("QR URL: " . $url);

            $result = Builder::create()
                ->writer(new PngWriter())
                ->writerOptions([])
                ->data($url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(300)
                ->margin(10)
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->validateResult(false)
                ->build();

            $qrFilePath = $fullPath . '/' . $code . '.png';
            Log::info("Saving QR to: " . $qrFilePath);

            $result->saveToFile($qrFilePath);

            // Verify file was created
            if (file_exists($qrFilePath)) {
                Log::info("QR code created successfully");
                return true;
            } else {
                Log::error("QR file doesn't exist after generation");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error generating QR code: " . $e->getMessage());
            return false;
        }
    }

    public function sendEmail()
    {
        $event = Event::where('id_event', 1)->first();
        $mail = new PHPMailer(true);
        try {
            $guestQrcode    = $_GET['guestQrcode'];
            $guestName      = $_GET['guestName'];
            $guestMail      = $_GET['guestMail'];
            if ($event->image_event != "") :
                $img = 'img/event/' . $event->image_event;
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
            } else {
                $mail->AddEmbeddedImage(public_path('asset/front/default.png'), 'imgtop');
            }

            // Use the consistent path constant
            $qrPath = public_path(self::QR_DIR . '/' . $guestQrcode . '.png');
            if (!file_exists($qrPath)) {
                $this->qrcodeGenerator($guestQrcode);
            }

            $mail->AddEmbeddedImage($qrPath, 'qrcode');

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = myEvent()->type_event;
            $mail->Body    = $this->linkGuestEmail($guestQrcode)->render();

            $mail->send();
            $mail->SmtpClose();

            Invitation::where('qrcode_invitation', $guestQrcode)->update(['send_email_invitation' => 1]);

            $status = "success";
            $message = "Berhasil mengirim email";
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $status = "error";
            $message = "Gagal mengirim ke email: " . $e->getMessage();
            Log::error("Email error: " . $e->getMessage());
        }
        return redirect("invite")->with($status, $message);
    }

    public function sendWhatsapp()
    {
    }

    public function index()
    {
        $invitations = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')
            ->orderBy('id_invitation', 'DESC')
            ->orderBy('name_guest', 'ASC')
            ->get();
        return view('invitation.index', compact('invitations'));
    }

    public function create()
    {
        $guests = Guest::orderBy('name_guest', 'ASC')
            ->orderBy('id_guest', 'DESC')
            ->with('invitation')->get();
        return view('invitation.create', compact('guests'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'guest'          => 'required',
            'type'           => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $qrcode = $this->generateCode();
        $this->qrcodeGenerator($qrcode);

        Invitation::create([
            "id_guest"                      => $request->guest,
            "qrcode_invitation"             => $qrcode,
            "table_number_invitation"       => $request->table_number,
            "type_invitation"               => $request->type,
            "information_invitation"        => $request->information,
            "link_invitation"               => '/invitation/' . $qrcode,
            "image_qrcode_invitation"       => '/' . self::QR_DIR . '/' . $qrcode . ".png", // use constant
            "id_event"                      => 1,
            "custom_message"               => $request->custom_message,
        ]);

        return redirect('/invite')->with('success', "Berhasil menambah data");
    }

    public function edit($id)
    {
        $invitation = Invitation::join('guest', 'guest.id_guest', '=', 'invitation.id_guest')->where('id_invitation', $id)->first();
        return view('invitation.edit', compact('invitation'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'custom_message' => 'nullable', // Ensure custom_message is allowed
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $invitation = Invitation::findOrFail($id);

        $invitation->update([
            'type_invitation' => $request->type,
            'table_number_invitation' => $request->table_number,
            'information_invitation' => $request->information,
            'custom_message' => $request->custom_message, // Update invitation's custom_message
        ]);

        // Update the related guest's custom_message
        $guest = $invitation->guest;
        if ($guest) {
            $guest->update([
                'custom_message' => $request->custom_message,
            ]);
        }

        return redirect('invite')->with('success', "Berhasil mengedit data");
    }

    public function delete(Request $request)
    {
        // Try to delete QR code from all possible paths
        $qrPaths = [
            public_path(self::QR_DIR . '/' . $request->qrcode . ".png"),
            public_path('img/QrCode/' . $request->qrcode . ".png"),
            public_path('img/qrCode/' . $request->qrcode . ".png")
        ];

        foreach ($qrPaths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
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

    // Guest Register Manual
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

        $guest = Guest::create([
            "name_guest"            => $request->name,
            "company_guest"         => $request->company,
            "email_guest"           => $request->email,
            "phone_guest"           => $request->phone,
            "address_guest"         => $request->address,
            "created_by_guest"      => "register",
        ]);

        $qrcode = $this->generateCode();
        $this->qrcodeGenerator($qrcode);

        Invitation::create([
            "id_guest"                      => $guest->id_guest,
            "qrcode_invitation"             => $qrcode,
            "type_invitation"               => "reguler",
            "link_invitation"               => '/invitation/' . $qrcode,
            "image_qrcode_invitation"       => '/' . self::QR_DIR . '/' . $qrcode . ".png", // use constant
            "id_event"                      => 1,
        ]);

        return redirect('/invitation/' . $qrcode)->with("register-success", "Terima kasih telah melakukan registrasi. Silahkan download QR untuk ditunjukan saat acara.");
    }
}
