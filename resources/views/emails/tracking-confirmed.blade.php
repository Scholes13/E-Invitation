<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Dibuka | {{ $invitation->name_guest }}</title>
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
        .success-icon {
            font-size: 54px;
            color: #28a745;
            text-align: center;
            margin: 20px 0;
        }
        .read-confirmation {
            text-align: center;
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 15px;
        }
        .button:hover {
            background-color: #0069d9;
        }
        .browser-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 20px;
            font-size: 14px;
            color: #6c757d;
        }
        .browser-info p {
            margin: 5px 0;
        }
        @keyframes checkmark {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .checkmark-circle {
            width: 100px;
            height: 100px;
            margin: 0 auto;
            background-color: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: checkmark 0.5s ease-in-out 0.7s both;
        }
        .checkmark {
            color: white;
            font-size: 60px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Undangan Dibuka</h1>
        </div>

        <div class="read-confirmation">
            <div class="checkmark-circle">
                <span class="checkmark">✓</span>
            </div>
            <h2>Email Berhasil Dikonfirmasi!</h2>
            <p>Halo {{ $invitation->name_guest }}, email undangan kamu berhasil dibuka dan ditandai sebagai dibaca.</p>
        </div>
        
        <div style="text-align: center;">
            <p>Terima kasih telah membuka email undangan kami. Status email kamu sudah diperbarui.</p>
            <p>Undangan kamu dengan kode: <strong>{{ $invitation->qrcode_invitation }}</strong></p>
            
            <a href="{{ url('/invitation/' . $invitation->qrcode_invitation) }}" class="button">Buka Undangan</a>
        </div>
        
        <div class="browser-info">
            <p><strong>Informasi Browser:</strong> {{ $browserInfo }}</p>
            <p><strong>Waktu Dibuka:</strong> {{ now()->format('d M Y H:i:s') }}</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $setting->name_app ?? 'QR Scan App' }}. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Automatically mark as read in parent tab's system if this was opened from email
        if (window.opener) {
            try {
                // Attempt to send a message to parent window that opened this one
                window.opener.postMessage({ 
                    type: 'EMAIL_READ_CONFIRMED',
                    qrcode: '{{ $invitation->qrcode_invitation }}',
                    time: '{{ now() }}'
                }, '*');
            } catch (e) {
                console.log('Could not communicate with parent window');
            }
        }
        
        // Send a tracking ping to ensure the server records this view
        fetch('{{ url("/track-email/{$invitation->qrcode_invitation}?t=" . time() . "&js=true") }}', {
            method: 'GET',
            mode: 'no-cors',
            cache: 'no-cache',
        }).catch(error => {
            console.log('Tracking ping sent');
        });
        
        // Record that this page was loaded successfully
        fetch('{{ url("/track-email/{$invitation->qrcode_invitation}?page_loaded=true&t=" . time()) }}', {
            method: 'GET',
            mode: 'no-cors',
            cache: 'no-cache',
        }).catch(error => {
            console.log('Page load tracking sent');
        });
    </script>
</body>
</html> 