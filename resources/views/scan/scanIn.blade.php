@extends('template.scan')
@section('content')

<title>Scan In - {{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title>

    <div class="container pt-5">
        <div class="form-group mt-5">
            <h2 class="text-light text-center">Scan In</h2>
            <div class="input-group">
                <div class="input-group-prepend camera-on" style="cursor: pointer">
                    <div class="input-group-text">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <input id="qrcode" type="text" class="form-control" autofocus autocomplete="off">
            </div>

            <div id="open-camera" class="row justify-content-center mt-4 d-none">
                <div class="col-lg-6">
                    <div class="card-body d-flex justify-content-center">
                        <div class="p-1 rounded" style="background-color: #6c3c0c">
                            <div id="my-camera"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="camera-error" class="alert alert-danger mt-2 d-none">
                <span id="error-message"></span>
            </div>
            <div class="row justify-content-center mt-2">
                <div class="col-lg-6">
                    <button id="test-camera" class="btn btn-sm btn-info">Test Kamera</button>
                    <span id="camera-status" class="ml-2 text-light"></span>
                </div>
            </div>
            <div class="row justify-content-center mt-3">
                <div class="col-lg-6 text-center">
                    <a href="{{ url('scan/modern') }}" class="btn btn-success">Gunakan Kamera Modern</a>
                    <a href="{{ url('scan/modern-qr') }}" class="btn btn-primary ml-2">
                        <i class="fas fa-qrcode mr-1"></i> Gunakan QR Auto Scanner
                    </a>
                    <p class="text-light mt-2"><small>Jika kamera tidak berfungsi, coba gunakan kamera modern atau QR auto scanner</small></p>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="{{ asset('plugin/webcamjs/webcam.min.js') }}"></script>
    <script>
        $(document).ready(function() {

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
                cameraOff();
            }

            function showError(message) {
                $("#error-message").text(message);
                $("#camera-error").removeClass('d-none');
            }

            function hideError() {
                $("#camera-error").addClass('d-none');
            }

            function checkCameraPermission() {
                return new Promise((resolve, reject) => {
                    navigator.mediaDevices.getUserMedia({ video: true })
                        .then(stream => {
                            // Release the stream immediately
                            stream.getTracks().forEach(track => track.stop());
                            resolve(true);
                        })
                        .catch(err => {
                            console.error("Camera permission error:", err);
                            reject(err);
                        });
                });
            }

            function cameraOn() {
                hideError();
                $("#open-camera").removeClass('d-none');
                $("#open-camera").addClass('on-cam');

                // Check for camera permission first
                checkCameraPermission()
                    .then(() => {
                        Webcam.set({
                            width: 500,
                            height: 375,
                            image_format: 'jpeg',
                            jpeg_quality: 90
                        });

                        Webcam.attach('#my-camera');

                        // Apply transform for front camera if needed
                        Webcam.on('live', function() {
                            try {
                                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                                    .then(stream => {
                                        const videoTrack = stream.getVideoTracks()[0];
                                        const capabilities = videoTrack.getCapabilities();
                                        
                                        // Release the test stream
                                        stream.getTracks().forEach(track => track.stop());

                                        if (capabilities.facingMode && capabilities.facingMode.includes('user')) {
                                            // Front-facing camera, apply horizontal flip
                                            document.getElementById('my-camera').querySelector('video').style.transform = 'scaleX(-1)';
                                        }
                                    })
                                    .catch(err => {
                                        console.error("Error accessing media devices.", err);
                                    });
                            } catch (err) {
                                console.error("Error in transform application:", err);
                            }
                        });

                        Webcam.on('error', function(err) {
                            console.error('Webcam error:', err);
                            showError('Error saat mengakses kamera: ' + err.toString());
                            cameraOff();
                        });
                    })
                    .catch(err => {
                        let errorMsg = "Tidak bisa mengakses kamera. ";
                        
                        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                            errorMsg += "Mohon berikan izin akses kamera pada browser Anda.";
                        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                            errorMsg += "Tidak ada kamera yang terdeteksi pada perangkat Anda.";
                        } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
                            errorMsg += "Kamera sedang digunakan oleh aplikasi lain.";
                        } else if (err.name === 'OverconstrainedError' || err.name === 'ConstraintNotSatisfiedError') {
                            errorMsg += "Kamera tidak memenuhi persyaratan yang dibutuhkan.";
                        } else if (err.name === 'SecurityError') {
                            errorMsg += "Penggunaan media tidak diizinkan karena alasan keamanan.";
                        } else {
                            errorMsg += "Error: " + (err.message || err.name || err.toString());
                        }
                        
                        showError(errorMsg);
                        console.error('Camera permission error:', err);
                        $("#open-camera").addClass('d-none');
                        $("#open-camera").removeClass('on-cam');
                    });
            }

            function cameraOff() {
                hideError();
                $("#open-camera").removeClass('on-cam');
                $("#open-camera").addClass('d-none');
                Webcam.reset();
            }

            // Test camera availability
            $("#test-camera").click(function() {
                $("#camera-status").html('<i class="fas fa-spinner fa-spin"></i> Memeriksa kamera...');
                
                navigator.mediaDevices.enumerateDevices()
                    .then(function(devices) {
                        let videoDevices = devices.filter(device => device.kind === 'videoinput');
                        
                        if (videoDevices.length === 0) {
                            $("#camera-status").html('<i class="fas fa-times text-danger"></i> Tidak ada kamera terdeteksi');
                            return;
                        }
                        
                        // Now check if we can actually access the camera
                        checkCameraPermission()
                            .then(() => {
                                $("#camera-status").html('<i class="fas fa-check text-success"></i> Kamera tersedia dan dapat diakses');
                                
                                // Show detected cameras
                                let cameraInfo = '<div class="mt-2 text-light"><strong>Kamera terdeteksi:</strong><ul>';
                                videoDevices.forEach((device, index) => {
                                    cameraInfo += `<li>${device.label || 'Kamera ' + (index + 1)}</li>`;
                                });
                                cameraInfo += '</ul></div>';
                                
                                // Show in status
                                $("#camera-status").after(cameraInfo);
                            })
                            .catch(err => {
                                $("#camera-status").html('<i class="fas fa-exclamation-triangle text-warning"></i> Kamera terdeteksi tetapi tidak dapat diakses');
                                showError("Kamera terdeteksi tetapi tidak dapat diakses: " + err.toString());
                            });
                    })
                    .catch(function(err) {
                        $("#camera-status").html('<i class="fas fa-times text-danger"></i> Error: ' + err.toString());
                    });
            });

            $(".camera-on").click(function() {
                var camera = $("#open-camera").hasClass('on-cam');
                camera ? cameraOff() : cameraOn();
            })

            $('#qrcode').on("keypress", function(e) {
                if (e.keyCode == 13) {

                    var qrcode = document.getElementById("qrcode").value;
                    var url = "{{ url('scan/in-process') }}";
                    var params = "?_token={{ csrf_token() }}&qrcode=" + qrcode;

                    if ($("#open-camera").hasClass('on-cam')) {
                        Webcam.snap(function(data_uri) {

                            fullUrl = url + params;

                            Webcam.upload(data_uri, fullUrl, function(status, res) {
                                try {
                                    const data = JSON.parse(res);
                                    customAlert(data);
                                } catch (err) {
                                    console.error("Error parsing response:", err, "Response:", res);
                                    customAlert({status: "error", message: "Error saat memproses respons: " + err.toString()});
                                }
                            });
                        });

                    } else {
                        $.ajax({
                            url: url,
                            method: "POST",
                            type: "JSON",
                            data: {
                                _token: "{{ csrf_token() }}",
                                qrcode: qrcode,
                            },
                            success: (res) => {
                                customAlert(res)
                            },
                            error: (err) => {
                                customAlert({status : "error", message: "Scan gagal"})
                            }
                        })

                    }

                }
            });

        })
    </script>
@endsection
