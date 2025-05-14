@extends('template.scan')
@section('content')

<title>Modern Camera Scan - {{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title>

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
        transform: scaleX(-1);
    }
    #camera-canvas {
        display: none;
    }
    .camera-controls {
        margin-top: 15px;
        text-align: center;
    }
</style>

<div class="container pt-5">
    <div class="form-group mt-5">
        <h2 class="text-light text-center">Modern Camera Scan</h2>
        <div class="input-group">
            <div class="input-group-prepend camera-toggle" style="cursor: pointer">
                <div class="input-group-text">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <input id="qrcode" type="text" class="form-control" autofocus autocomplete="off">
        </div>

        <div id="camera-container" class="row justify-content-center mt-4 d-none">
            <div class="col-lg-8">
                <div class="card-body">
                    <div id="video-container">
                        <video id="camera-video" autoplay playsinline></video>
                        <canvas id="camera-canvas"></canvas>
                    </div>
                    <div class="camera-controls">
                        <button id="change-camera" class="btn btn-sm btn-secondary">Ganti Kamera</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="camera-error" class="alert alert-danger mt-2 d-none">
            <span id="error-message"></span>
        </div>
        
        <div class="row justify-content-center mt-2">
            <div class="col-lg-8 text-center">
                <a href="{{ url('scan/modern-qr') }}" class="btn btn-success">
                    <i class="fas fa-qrcode mr-1"></i> Gunakan QR Scanner Otomatis
                </a>
                <p class="text-light mt-2"><small>Gunakan scanner otomatis untuk mendeteksi QR secara langsung tanpa input manual</small></p>
            </div>
        </div>
        
        <div class="row justify-content-center mt-2">
            <div class="col-lg-6">
                <button id="use-modern" class="btn btn-success">Gunakan Kamera Modern</button>
                <button id="use-legacy" class="btn btn-secondary ml-2">Gunakan Kamera Legacy</button>
                <span id="camera-status" class="ml-2 text-light"></span>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Variables
        let videoElement = document.getElementById('camera-video');
        let canvasElement = document.getElementById('camera-canvas');
        let videoContext = canvasElement.getContext('2d');
        let currentStream = null;
        let availableCameras = [];
        let currentCameraIndex = 0;
        let cameraActive = false;
        
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
            
            $('#qrcode').val('');
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
                        height: { ideal: 375 }
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
                };
                
                $("#camera-container").removeClass('d-none');
                cameraActive = true;
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
        
        // Stop camera
        function stopCamera() {
            if (currentStream) {
                currentStream.getTracks().forEach(track => track.stop());
                currentStream = null;
            }
            
            $("#camera-container").addClass('d-none');
            cameraActive = false;
        }
        
        // Take picture and submit
        function takePicture() {
            if (!cameraActive || !currentStream) {
                customAlert({status: "error", message: "Kamera tidak aktif"});
                return;
            }
            
            try {
                // Draw current video frame to canvas
                videoContext.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);
                
                // Get as data URL
                const imageDataUrl = canvasElement.toDataURL('image/jpeg', 0.9);
                
                // Send to server
                const qrcode = document.getElementById("qrcode").value;
                const url = "{{ url('scan/in-process') }}";
                
                // Convert data URL to Blob for FormData
                fetch(imageDataUrl)
                    .then(res => res.blob())
                    .then(blob => {
                        const formData = new FormData();
                        formData.append('_token', "{{ csrf_token() }}");
                        formData.append('qrcode', qrcode);
                        formData.append('webcam', blob, 'snapshot.jpg');
                        
                        // Send to server
                        $.ajax({
                            url: url,
                            method: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: (res) => {
                                customAlert(res);
                            },
                            error: (err) => {
                                console.error("Error submitting image:", err);
                                customAlert({status: "error", message: "Scan gagal: " + err.statusText});
                            }
                        });
                    })
                    .catch(err => {
                        console.error("Error converting data URL:", err);
                        customAlert({status: "error", message: "Error memproses gambar: " + err.toString()});
                    });
            } catch (error) {
                console.error("Error taking picture:", error);
                customAlert({status: "error", message: "Error mengambil gambar: " + error.toString()});
            }
        }
        
        // Toggle camera
        $(".camera-toggle").click(function() {
            if (cameraActive) {
                stopCamera();
            } else {
                startCamera();
            }
        });
        
        // Change camera button
        $("#change-camera").click(async function() {
            if (availableCameras.length <= 1) {
                showError("Hanya ada satu kamera yang tersedia");
                return;
            }
            
            currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
            await startCamera();
        });
        
        // Redirect to legacy camera view
        $("#use-legacy").click(function() {
            window.location.href = "{{ url('scan/in') }}";
        });
        
        // Handle QR code input
        $('#qrcode').on("keypress", function(e) {
            if (e.keyCode == 13) {
                const qrcode = document.getElementById("qrcode").value;
                
                if (cameraActive) {
                    takePicture();
                } else {
                    // No camera active, use text-only submission
                    const url = "{{ url('scan/in-process') }}";
                    
                    $.ajax({
                        url: url,
                        method: "POST",
                        type: "JSON",
                        data: {
                            _token: "{{ csrf_token() }}",
                            qrcode: qrcode,
                        },
                        success: (res) => {
                            customAlert(res);
                        },
                        error: (err) => {
                            customAlert({status: "error", message: "Scan gagal"});
                        }
                    });
                }
            }
        });
    });
</script>
@endsection 