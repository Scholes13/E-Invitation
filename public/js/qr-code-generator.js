import QRCodeStyling from "qr-code-styling";

// Function to generate QR code with custom styling
function generateCustomQR(data, options = {}) {
    const defaultOptions = {
        width: 300,
        height: 300,
        type: "svg",
        data: data,
        image: "", // Logo URL
        margin: 10,
        qrOptions: {
            typeNumber: 0,
            mode: "Byte",
            errorCorrectionLevel: "H"
        },
        imageOptions: {
            hideBackgroundDots: true,
            imageSize: 0.4,
            margin: 10,
            crossOrigin: "anonymous",
        },
        dotsOptions: {
            color: "#000000",
            type: "square",
            gradient: null
        },
        backgroundOptions: {
            color: "#ffffff",
        },
        cornersSquareOptions: {
            color: "#000000",
            type: "square",
        },
        cornersDotOptions: {
            color: "#000000",
            type: "square",
        },
    };

    // Merge default options with custom options
    const mergedOptions = { ...defaultOptions, ...options };
    
    return new QRCodeStyling(mergedOptions);
}

// Make functions available globally
window.generateCustomQR = generateCustomQR; 