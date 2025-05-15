        // Add input change listeners for all form elements
        const formInputs = document.querySelectorAll('#qrGeneratorForm input, #qrGeneratorForm select');
        formInputs.forEach(input => {
            input.addEventListener('change', function() {
                generateQR();
            });
        });
        
        // Link width and height fields to maintain a square QR code
        document.getElementById('qrWidth').addEventListener('change', function() {
            // Update height to match width
            document.getElementById('qrHeight').value = this.value;
        });
        
        document.getElementById('qrHeight').addEventListener('change', function() {
            // Update width to match height
            document.getElementById('qrWidth').value = this.value;
        });
        
        // Get all QR options from form fields 