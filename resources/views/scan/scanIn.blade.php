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

            function cameraOn() {
                $("#open-camera").removeClass('d-none');
                $("#open-camera").addClass('on-cam');
                Webcam.set({
                    width: 500,
                    height: 375,
                    image_format: 'jpeg',
                    jpeg_quality: 90,
                    flip_horiz: true
                });
                Webcam.attach('#my-camera');
            }

            function cameraOff() {
                $("#open-camera").removeClass('on-cam');
                $("#open-camera").addClass('d-none');
                Webcam.reset();
            }

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
                                const data = JSON.parse(res);
                                customAlert(data)
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
