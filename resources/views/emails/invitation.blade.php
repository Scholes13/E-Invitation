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
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        @if(isset($customTemplate) && !empty($customTemplate))
            {!! $customTemplate !!}
        @else
            <p>Dear {{ $invitation->name_guest }},</p>
            <p>We are delighted to invite you to our event.</p>
            <p>Your personal invitation code is: {{ $invitation->qrcode_invitation }}</p>
            <p>Thank You</p>
        @endif
    </div>
</body>
</html>