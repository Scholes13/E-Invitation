<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan untuk {{ $invitation->name_guest }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        h1 {
            color: #007bff;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        .read-confirmation {
            text-align: center;
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Undangan untuk {{ $invitation->name_guest }}</h1>
        </div>

        <div class="read-confirmation">
            Email ini telah dibuka dan ditandai sebagai dibaca.
        </div>
        
        <div class="content">
            {!! $content !!}
        </div>
        
        <div class="footer">
            <p>Email ini dikirim ke {{ $invitation->email_guest }}.</p>
            <p>&copy; {{ date('Y') }} QR Scan App. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 