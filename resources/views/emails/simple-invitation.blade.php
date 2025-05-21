<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Undangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #e4e4e4;
            border-radius: 5px;
        }
        h1 {
            color: #3498db;
            margin-top: 0;
        }
        p {
            margin-bottom: 16px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888888;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Undangan</h1>
        
        <p>Dear {{ $invitation->name_guest }},</p>
        
        <p>We are delighted to invite you to our event.</p>
        
        <p>Your personal invitation code is: {{ $invitation->qrcode_invitation }}</p>
        
        <p>Thank You</p>
        
        <div class="footer">
            <p>© {{ date('Y') }} {{ $companyName ?? 'Werkudara Group' }}</p>
        </div>
    </div>
</body>
</html> 