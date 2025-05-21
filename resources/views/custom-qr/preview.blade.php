<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Preview</title>
    <style>
        body {
            background: #000;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        
        #qrcode-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        #qrcode {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        
        #qrcode > svg,
        #qrcode > img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        
        .error-message {
            color: #e74c3c;
            text-align: center;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div id="qrcode-container">
        <div id="qrcode"></div>
    </div>

    <!-- QR Code Styling library -->
    <script src="{{ asset('js/qr-code-styling-fix.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for QR code styling library to be ready
            document.addEventListener('qr-code-styling-ready', function() {
                console.log('QR Code Styling library is ready');
                try {
                    // Parse the template settings
                    let templateSettings = @json($template->settings_json ? json_decode($template->settings_json) : null);
                    const sampleData = @json($sampleData);
                    
                    // Convert settings string to object if needed
                    if (templateSettings && typeof templateSettings === 'string') {
                        try {
                            templateSettings = JSON.parse(templateSettings);
                        } catch (e) {
                            console.error('Failed to parse settings JSON string:', e);
                            templateSettings = null;
                        }
                    }
                    
                    if (!templateSettings) {
                        // Create default settings that match Endroid's defaults
                        templateSettings = {
                            width: 300,
                            height: 300,
                            type: 'svg',
                            data: sampleData,
                            margin: 10,
                            qrOptions: {
                                typeNumber: 0,
                                mode: 'Byte',
                                errorCorrectionLevel: 'H'
                            },
                            dotsOptions: {
                                color: '#000000',
                                type: 'square'
                            },
                            backgroundOptions: {
                                color: '#ffffff'
                            }
                        };
                        
                        // Add logo if present
                        @if($template->logo_path)
                        const logoUrl = '{{ Storage::url(str_replace("public/", "", $template->logo_path)) }}';
                        templateSettings.image = '{{ url('/') }}' + logoUrl;
                        templateSettings.imageOptions = {
                            hideBackgroundDots: true,
                            imageSize: 0.3,
                            margin: 10,
                            crossOrigin: 'anonymous'
                        };
                        @endif
                    } else {
                        // Ensure existing template settings match Endroid defaults
                        templateSettings.width = 300;
                        templateSettings.height = 300;
                        templateSettings.margin = 10;
                        if (templateSettings.qrOptions) {
                            templateSettings.qrOptions.errorCorrectionLevel = 'H';
                        }
                    }
                    
                    // Update the data in settings
                    templateSettings.data = sampleData;
                    
                    // Initialize QR code with settings
                    const qrCode = new QRCodeStyling(templateSettings);
                    
                    // Clear container first
                    const container = document.getElementById("qrcode");
                    container.innerHTML = '';
                    
                    // Render the QR code to the container
                    qrCode.append(container);
                    
                    // Ensure SVG is centered
                    setTimeout(function() {
                        const svg = container.querySelector('svg');
                        if (svg) {
                            svg.style.display = 'block';
                            svg.style.margin = '0 auto';
                        }
                    }, 100);
                    
                    // Log the settings for debugging
                    console.log('QR Code Settings:', templateSettings);
                } catch (error) {
                    console.error('Error generating QR code:', error);
                    
                    // Show error to user
                    const container = document.getElementById('qrcode-container');
                    container.innerHTML = `
                        <div class="error-message">
                            <strong>Error generating QR code:</strong><br>
                            ${error.message}
                        </div>
                    `;
                }
            });
        });
    </script>
</body>
</html> 