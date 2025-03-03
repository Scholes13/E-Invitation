<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Undangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #333;
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #333;">
    <div>
        Dear {{ $invitation->name_guest }},<br>
        <br>
        @if($template = \App\Models\Setting::first()->email_body_template)
            {!! $template !!}
        @else
            <p>Dear {{ $invitation->name_guest }},</p>
            <p>We are delighted to invite you to our event.</p>
            <p>Your personal invitation code is: {{ $invitation->qrcode_invitation }}</p>
            <p>Thank You</p>
        @endif
    </div>
</body>
</html>