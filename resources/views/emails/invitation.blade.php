<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>Undangan</title>
    <style type="text/css" rel="stylesheet" media="all">
        /* Base styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            line-height: 1.5;
            color: #333333;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            width: 100% !important;
        }
        
        table {
            border-spacing: 0;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        td {
            padding: 0;
        }
        
        img {
            border: 0;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
            height: auto;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        
        .button {
            background-color: #3498db;
            border-radius: 5px;
            color: #ffffff;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-align: center;
        }
        
        /* Media queries for responsive design */
        @media only screen and (max-width: 600px) {
            .container {
                padding: 10px !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table role="presentation" class="container" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                            @if(isset($customTemplate) && !empty($customTemplate))
                                {!! $customTemplate !!}
                            @else
                                <p>Dear {{ $invitation->name_guest }},</p>
                                <p>We are delighted to invite you to our event.</p>
                                <p>Your personal invitation code is: {{ $invitation->qrcode_invitation }}</p>
                                <p>Thank You</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>