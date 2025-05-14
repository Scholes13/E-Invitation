@extends('template.scan')
@section('content')

<title>QR Scanner Out - {{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title>

<style>
    #video-container {
        position: relative;
        width: 500px;
        height: 375px;
        margin: 0 auto;
        border: 2px solid #6c3c0c;
        border-radius: 5px;
        overflow: hidden;
    }
    #camera-video {
        width: 100%;
        height: 100%;
        /* Tidak menggunakan mirror effect */
        /* transform: scaleX(-1); */
    }
    #camera-canvas {
        display: none;
    }
    .camera-controls {
        margin-top: 15px;
        text-align: center;
    }
    #scan-result {
        display: none;
        margin-top: 15px;
        padding: 10px;
        border-radius: 5px;
        background-color: rgba(255, 255, 255, 0.1);
    }
    .scan-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 2px solid transparent;
        box-sizing: border-box;
        pointer-events: none;
        z-index: 10;
    }
    .scan-highlight {
        border-color: #28a745;
        animation: pulse 1s infinite;
    }
    .cooldown-active {
        border-color: #dc3545;
        animation: pulse-red 1s infinite;
    }
    @keyframes pulse {
        0% { border-color: rgba(40, 167, 69, 0.5); }
        50% { border-color: rgba(40, 167, 69, 1); }
        100% { border-color: rgba(40, 167, 69, 0.5); }
    }
    @keyframes pulse-red {
        0% { border-color: rgba(220, 53, 69, 0.5); }
        50% { border-color: rgba(220, 53, 69, 1); }
        100% { border-color: rgba(220, 53, 69, 0.5); }
    }
    .loader {
        display: none;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #6c3c0c;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: 15px auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    #cooldown-info {
        display: none;
        margin-top: 10px;
        padding: 5px 10px;
        text-align: center;
        background-color: rgba(220, 53, 69, 0.2);
        border-radius: 5px;
        color: #fff;
    }
    #last-scan-info {
        margin-top: 15px;
        padding: 10px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 5px;
        color: #fff;
        display: none;
    }
</style>

<div class="container pt-5">
    <div class="form-group mt-5">
        <h2 class="text-light text-center">QR Scanner Out</h2>
        
        <div class="row justify-content-center mt-4">
            <div class="col-lg-8">
                <div class="card-body">
                    <div id="video-container">
                        <video id="camera-video" autoplay playsinline></video>
                        <canvas id="camera-canvas"></canvas>
                        <div class="scan-overlay" id="scan-overlay"></div>
                    </div>
                    <div class="loader" id="processing-loader"></div>
                    <div id="scan-result" class="text-light text-center"></div>
                    <div id="cooldown-info" class="text-light"></div>
                    <div id="last-scan-info" class="text-light text-center"></div>
                    <div class="camera-controls">
                        <button id="change-camera" class="btn btn-secondary">Ganti Kamera</button>
                        <button id="toggle-mirror" class="btn btn-info ml-2">Toggle Mirror</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="camera-error" class="alert alert-danger mt-2 d-none">
            <span id="error-message"></span>
        </div>
    </div>
</div>

<!-- Import jsQR library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<script>
    $(document).ready(function() {
        // Variables
        let videoElement = document.getElementById('camera-video');
        let canvasElement = document.getElementById('camera-canvas');
        let scanOverlay = document.getElementById('scan-overlay');
        let scanResult = document.getElementById('scan-result');
        let processingLoader = document.getElementById('processing-loader');
        let cooldownInfo = document.getElementById('cooldown-info');
        let lastScanInfo = document.getElementById('last-scan-info');
        let videoContext = canvasElement.getContext('2d', { willReadFrequently: true });
        let currentStream = null;
        let availableCameras = [];
        let currentCameraIndex = 0;
        let scanning = false;
        let lastResult = null;
        let scanTimeout = null;
        let cooldownTimeout = null;
        let isMirrored = false;
        let cooldownActive = false;
        
        // Cooldown untuk mencegah scan berulang
        const SCAN_COOLDOWN = 10000; // 10 detik cooldown
        const SCAN_HISTORY_KEY = 'qr_scan_out_history';
        
        // Load scan history from localStorage
        function loadScanHistory() {
            const historyJSON = localStorage.getItem(SCAN_HISTORY_KEY);
            return historyJSON ? JSON.parse(historyJSON) : {};
        }
        
        // Save scan to history
        function saveScanToHistory(qrCode, timestamp) {
            let history = loadScanHistory();
            history[qrCode] = timestamp;
            localStorage.setItem(SCAN_HISTORY_KEY, JSON.stringify(history));
        }
        
        // Check if QR code was recently scanned
        function wasRecentlyScanned(qrCode) {
            const history = loadScanHistory();
            const lastScanTime = history[qrCode];
            
            if (!lastScanTime) return false;
            
            const now = new Date().getTime();
            const elapsed = now - lastScanTime;
            
            return elapsed < SCAN_COOLDOWN;
        }
        
        // Format time as readable string
        function formatTimeAgo(timestamp) {
            const now = new Date().getTime();
            const elapsed = now - timestamp;
            
            if (elapsed < 60000) {
                return `${Math.floor(elapsed / 1000)} detik yang lalu`;
            } else if (elapsed < 3600000) {
                return `${Math.floor(elapsed / 60000)} menit yang lalu`;
            } else {
                return `${Math.floor(elapsed / 3600000)} jam yang lalu`;
            }
        }
        
        // Display last scan info
        function showLastScanInfo(qrCode) {
            const history = loadScanHistory();
            const lastScanTime = history[qrCode];
            
            if (lastScanTime) {
                const timeAgo = formatTimeAgo(lastScanTime);
                lastScanInfo.innerHTML = `<strong>Terakhir scan:</strong> ${timeAgo}<br><small>QR: ${qrCode}</small>`;
                lastScanInfo.style.display = 'block';
            }
        }
        
        // Start cooldown timer display
        function startCooldownTimer(qrCode, duration) {
            cooldownActive = true;
            scanOverlay.classList.add('cooldown-active');
            
            let timeLeft = Math.ceil(duration / 1000);
            updateCooldownDisplay(timeLeft, qrCode);
            
            const timerInterval = setInterval(() => {
                timeLeft--;
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    cooldownInfo.style.display = 'none';
                    scanOverlay.classList.remove('cooldown-active');
                    cooldownActive = false;
                } else {
                    updateCooldownDisplay(timeLeft, qrCode);
                }
            }, 1000);
        }
        
        // Update cooldown display
        function updateCooldownDisplay(seconds, qrCode) {
            cooldownInfo.innerHTML = `QR telah di-scan! Tunggu <strong>${seconds}</strong> detik untuk scan ulang.<br><small>QR: ${qrCode}</small>`;
            cooldownInfo.style.display = 'block';
        }
        
        // Show/hide error message
        function showError(message) {
            $("#error-message").text(message);
            $("#camera-error").removeClass('d-none');
        }
        
        function hideError() {
            $("#camera-error").addClass('d-none');
        }
        
        // Alert function
        function customAlert(data) {
            if (data.status == "success") {
                Swal.fire({
                    title: "Scan Berhasil",
                    text: data.message,
                    icon: "success",
                    confirmButtonColor: "#6F4E37",
                });
            } else if (data.status == "warning") {
                Swal.fire({
                    title: "Peringatan",
                    text: data.message,
                    icon: "warning",
                    confirmButtonColor: "#6F4E37",
                });
            } else {
                Swal.fire({
                    title: "Gagal",
                    text: data.message,
                    icon: "error",
                    confirmButtonColor: "#6F4E37",
                });
            }
            
            resetScanState();
        }
        
        function resetScanState() {
            setTimeout(() => {
                scanning = false;
                scanOverlay.classList.remove("scan-highlight");
                scanResult.style.display = "none";
                processingLoader.style.display = "none";
            }, 1000);
        }
        
        // Get available cameras
        async function getAvailableCameras() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                return devices.filter(device => device.kind === 'videoinput');
            } catch (error) {
                console.error("Error enumerating devices:", error);
                showError("Tidak dapat mengakses daftar kamera: " + error.toString());
                return [];
            }
        }
        
        // Start camera
        async function startCamera() {
            try {
                hideError();
                
                // Get cameras if we don't have them yet
                if (availableCameras.length === 0) {
                    availableCameras = await getAvailableCameras();
                    if (availableCameras.length === 0) {
                        showError("Tidak ada kamera yang terdeteksi");
                        return false;
                    }
                }
                
                // Stop any existing stream
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                }
                
                // Get the camera constraints
                const cameraId = availableCameras[currentCameraIndex].deviceId;
                const constraints = {
                    video: {
                        deviceId: cameraId ? { exact: cameraId } : undefined,
                        width: { ideal: 500 },
                        height: { ideal: 375 },
                        facingMode: { ideal: "environment" } // Prefer back camera
                    },
                    audio: false
                };
                
                // Start the stream
                currentStream = await navigator.mediaDevices.getUserMedia(constraints);
                videoElement.srcObject = currentStream;
                
                // Set canvas size based on video dimensions
                videoElement.onloadedmetadata = () => {
                    canvasElement.width = videoElement.videoWidth;
                    canvasElement.height = videoElement.videoHeight;
                    
                    // Start scanning
                    requestAnimationFrame(scanQRCode);
                };
                
                return true;
            } catch (error) {
                console.error("Error starting camera:", error);
                let errorMsg = "Tidak bisa mengakses kamera. ";
                
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    errorMsg += "Mohon berikan izin akses kamera pada browser Anda.";
                } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                    errorMsg += "Tidak ada kamera yang terdeteksi pada perangkat Anda.";
                } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
                    errorMsg += "Kamera sedang digunakan oleh aplikasi lain.";
                } else {
                    errorMsg += "Error: " + error.toString();
                }
                
                showError(errorMsg);
                return false;
            }
        }
        
        // Scan QR Code from video stream
        function scanQRCode() {
            if (!currentStream || scanning || cooldownActive) {
                requestAnimationFrame(scanQRCode);
                return;
            }
            
            // Draw current frame to canvas
            videoContext.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);
            const imageData = videoContext.getImageData(0, 0, canvasElement.width, canvasElement.height);
            
            // Scan for QR code
            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });
            
            if (code && code.data) {
                // Check if this QR was recently scanned
                if (wasRecentlyScanned(code.data)) {
                    // QR code was recently scanned, show cooldown timer
                    if (!cooldownActive) {
                        const history = loadScanHistory();
                        const lastScanTime = history[code.data];
                        const now = new Date().getTime();
                        const timeLeft = SCAN_COOLDOWN - (now - lastScanTime);
                        
                        if (timeLeft > 0) {
                            startCooldownTimer(code.data, timeLeft);
                            showLastScanInfo(code.data);
                        }
                    }
                } 
                // Avoid duplicate scans
                else if (lastResult !== code.data) {
                    console.log("QR Code detected:", code.data);
                    
                    // Highlight QR code location
                    scanOverlay.classList.add("scan-highlight");
                    
                    // Show result
                    scanResult.textContent = "QR Code: " + code.data;
                    scanResult.style.display = "block";
                    
                    // Set scan in progress
                    scanning = true;
                    lastResult = code.data;
                    
                    // Process the QR code
                    processingLoader.style.display = "block";
                    processQRCode(code.data);
                    
                    // Take a snapshot for upload
                    takePictureAndSubmit(code.data);
                    
                    // Save scan to history with current timestamp
                    const now = new Date().getTime();
                    saveScanToHistory(code.data, now);
                    
                    // Reset lastResult after cooldown
                    scanTimeout = setTimeout(() => {
                        lastResult = null;
                    }, SCAN_COOLDOWN);
                }
            }
            
            requestAnimationFrame(scanQRCode);
        }
        
        // Process QR code
        function processQRCode(qrCode) {
            console.log("Processing QR code:", qrCode);
            // This is just a visual indicator, actual submission happens in takePictureAndSubmit
        }
        
        // Take picture and submit QR code
        function takePictureAndSubmit(qrCode) {
            try {
                // Get current frame as image
                const imageDataUrl = canvasElement.toDataURL('image/jpeg', 0.9);
                const url = "{{ url('scan/out-process') }}";
                
                // Convert data URL to Blob for FormData
                fetch(imageDataUrl)
                    .then(res => res.blob())
                    .then(blob => {
                        const formData = new FormData();
                        formData.append('_token', "{{ csrf_token() }}");
                        formData.append('qrcode', qrCode);
                        formData.append('webcam', blob, 'snapshot.jpg');
                        
                        // Send to server
                        $.ajax({
                            url: url,
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: (res) => {
                                processingLoader.style.display = "none";
                                customAlert(res);
                                
                                // After successful or warning response, start cooldown
                                if (res.status === "success" || res.status === "warning") {
                                    startCooldownTimer(qrCode, SCAN_COOLDOWN);
                                }
                            },
                            error: (err) => {
                                processingLoader.style.display = "none";
                                console.error("Error submitting image:", err);
                                customAlert({status: "error", message: "Scan gagal: " + err.statusText});
                            }
                        });
                    })
                    .catch(err => {
                        processingLoader.style.display = "none";
                        console.error("Error converting data URL:", err);
                        customAlert({status: "error", message: "Error memproses gambar: " + err.toString()});
                    });
            } catch (error) {
                processingLoader.style.display = "none";
                console.error("Error taking picture:", error);
                customAlert({status: "error", message: "Error mengambil gambar: " + error.toString()});
            }
        }
        
        // Change camera button
        $("#change-camera").click(async function() {
            if (availableCameras.length <= 1) {
                showError("Hanya ada satu kamera yang tersedia");
                return;
            }
            
            currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
            resetScanState();
            await startCamera();
        });
        
        // Toggle mirror effect
        $("#toggle-mirror").click(function() {
            isMirrored = !isMirrored;
            if (isMirrored) {
                videoElement.style.transform = "scaleX(-1)";
                $(this).addClass('active');
            } else {
                videoElement.style.transform = "none";
                $(this).removeClass('active');
            }
        });
        
        // Initialize camera on page load
        startCamera();
    });
</script>
@endsection 