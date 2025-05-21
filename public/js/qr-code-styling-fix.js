// QR Code Styling Fix
// This script loads the QR Code Styling library from CDN
// and ensures it's properly defined globally

// First check if QRCodeStyling is already defined
if (typeof QRCodeStyling === 'undefined') {
    console.log('Loading QR Code Styling from CDN...');
    
    // Create a script element
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/qr-code-styling@1.9.0/lib/qr-code-styling.js';
    script.async = false; // Important to maintain synchronous loading
    
    // Add error handling
    script.onerror = function() {
        console.error('Failed to load QR Code Styling library from CDN');
        alert('Failed to load QR Code Styling library. Please check your internet connection and try again.');
    };
    
    // Append the script to the document
    document.head.appendChild(script);
    
    // Define a function to check if the library has loaded
    function checkLibraryLoaded() {
        if (typeof QRCodeStyling === 'undefined') {
            console.warn('QRCodeStyling not defined yet, retrying...');
            setTimeout(checkLibraryLoaded, 100);
        } else {
            console.log('QR Code Styling library loaded successfully');
            
            // Patch QRCodeStyling with our own wrapper to fix style issues
            patchQRCodeStyling();
            
            // Dispatch an event to notify the application that the library is ready
            document.dispatchEvent(new Event('qr-code-styling-ready'));
        }
    }
    
    // Start checking if the library has loaded
    script.onload = checkLibraryLoaded;
} else {
    console.log('QR Code Styling library already loaded');
    
    // Patch QRCodeStyling with our own wrapper
    patchQRCodeStyling();
    
    // Dispatch the ready event immediately if the library is already loaded
    document.dispatchEvent(new Event('qr-code-styling-ready'));
}

// Function to patch QRCodeStyling for better compatibility
function patchQRCodeStyling() {
    if (typeof QRCodeStyling !== 'undefined') {
        // Store the original constructor
        const OriginalQRCodeStyling = QRCodeStyling;
        
        // Store original download method 
        const originalDownload = OriginalQRCodeStyling.prototype.download;
        
        // Create a wrapper constructor
        window.QRCodeStyling = function(options) {
            // Create a deep copy of the options
            const patchedOptions = JSON.parse(JSON.stringify(options));
            
            // Fix dot type issues - validate that the dot type is supported
            if (patchedOptions.dotsOptions && patchedOptions.dotsOptions.type) {
                // Only allow known supported dot types
                const validTypes = ['square', 'dots', 'rounded', 'classy'];
                
                if (!validTypes.includes(patchedOptions.dotsOptions.type)) {
                    console.warn(`Unsupported dot type: ${patchedOptions.dotsOptions.type}, falling back to 'square'`);
                    patchedOptions.dotsOptions.type = 'square';
                }
                
                console.log(`Using dot type: ${patchedOptions.dotsOptions.type}`);
            }
            
            // Ensure corners square options are consistent
            if (patchedOptions.cornersSquareOptions) {
                // Validate corner square type
                const validCornerTypes = ['square', 'dot', 'extra-rounded'];
                if (!validCornerTypes.includes(patchedOptions.cornersSquareOptions.type)) {
                    console.warn(`Unsupported corner square type: ${patchedOptions.cornersSquareOptions.type}, falling back to 'square'`);
                    patchedOptions.cornersSquareOptions.type = 'square';
                }
            }
            
            // Add default cornersDotOptions if not present
            if (!patchedOptions.cornersDotOptions) {
                patchedOptions.cornersDotOptions = {
                    type: 'square',
                    color: patchedOptions.cornersSquareOptions?.color || '#000000'
                };
                console.log('Adding default cornersDotOptions');
            }
            
            // Normalize image options for consistency
            if (patchedOptions.image && patchedOptions.imageOptions) {
                // Ensure imageSize is within reasonable bounds
                if (typeof patchedOptions.imageOptions.imageSize !== 'undefined') {
                    const size = parseFloat(patchedOptions.imageOptions.imageSize);
                    if (isNaN(size) || size < 0.01 || size > 0.9) {
                        patchedOptions.imageOptions.imageSize = 0.3; // Default to 30% if invalid
                    }
                }
                
                // Ensure margin is reasonable
                if (typeof patchedOptions.imageOptions.margin !== 'undefined') {
                    const margin = parseInt(patchedOptions.imageOptions.margin);
                    if (isNaN(margin) || margin < 0 || margin > 50) {
                        patchedOptions.imageOptions.margin = 10; // Default to 10px if invalid
                    }
                }
                
                // Always set crossOrigin for consistency
                patchedOptions.imageOptions.crossOrigin = 'anonymous';
            }
            
            // Call the original constructor with our patched options
            return new OriginalQRCodeStyling(patchedOptions);
        };
        
        // Copy prototype from original
        window.QRCodeStyling.prototype = Object.create(OriginalQRCodeStyling.prototype);
        
        // Override the download method to ensure consistency
        window.QRCodeStyling.prototype.download = function(options = {}) {
            // Make a copy of the current options for downloading
            const currentOptions = JSON.parse(JSON.stringify(this._options));
            
            // Force type to png for download for better consistency
            if (!options.type) {
                options.type = 'png';
            }
            
            // Use original download method
            return originalDownload.call(this, options);
        };
        
        // Log patching complete
        console.log('QR code styling library patched for better compatibility');
    }
} 