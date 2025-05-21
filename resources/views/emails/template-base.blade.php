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
        
        .content {
            padding: 20px;
            font-size: 16px;
            line-height: 1.6;
        }
        
        /* Ensure links are visible */
        a {
            color: #3498db !important;
            text-decoration: underline !important;
        }
        
        /* Media queries for responsive design */
        @media only screen and (max-width: 600px) {
            .container {
                padding: 10px !important;
                width: 100% !important;
            }
            .content {
                padding: 10px !important;
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
                        <td class="content">
                            {!! $content !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 