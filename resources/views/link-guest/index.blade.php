<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{{ $event->type_event }} - {{ $event->name_event }}</title>
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('template/node_modules/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/node_modules/@fortawesome/fontawesome-free/css/all.css') }}">
    <!-- JS Libraies -->
    <link rel="stylesheet" href="{{ asset('plugin/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/node_modules/izitoast/dist/css/iziToast.min.css') }}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/components.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ mySetting()->logo_app != '' ? asset('img/app/'.mySetting()->logo_app) : asset('template/assets/img/logo.png') }}">
    <style>
        .custom-bg {
            @if ($event->image_bg_event != '' && $event->image_bg_status == 1)
                background-image: url("{{ asset('img/event/' . $event->image_bg_event) }}");
                background-size: cover;
				background-position: center;
            @endif
			background-color: {{ $event->color_bg_event ?? "#6c3c0c" }};
        }
    </style>
</head>

<body class="custom-bg">
    <script src="{{ asset('template/node_modules/jquery/dist/jquery.min.js') }}"></script>

    <div id="app">
        <div class="main-wrapper">
			<div class="container-fluid">

            <div class="row" style="color: {{ $event->color_text_event ?? "#e3eaef" }}">

				{{-- <div class="col-sm-3 d-none d-lg-block"></div> --}}

				<div class="col-lg-12">

					<div class="text-center">
						<div class="mt-3">
						@php
							if ($event->image_event != ''):
								if (file_exists(public_path('img/event/' . $event->image_event))) {
									$img = asset('img/event/' . $event->image_event);
								} else {
									$img = asset('asset/front/image-not-found.jpg');
								}
							else:
								$img = asset('asset/front/default.png');
							endif;
						@endphp
						@if ($event->image_top_status == 1)
							<img src="{{ $img }}" style="max-width: 100%; width: 200px; height: auto; border: 0px solid #eee;" alt="">
						@endif
						</div>
						<div class="mt-3 text-center">
							<div class="h5">{{ $event->type_event }}</div>
							<div class="h2" style="margin-top:5px">
								{!! nl2br($event->name_event) !!}
							</div>
							<div style="margin:25px 0 20px 0;">
								<div class="h6 mb-0">
									<i>Dear</i>
								</div>
								<div class="h5 bd-highlight">
									{{ $invt->name_guest }}
								</div>
							</div>
						</div>

						<div class="d-flex justify-content-center py-3">
							<div class="mx-3 text-right">
								<h5>{{ \Carbon\Carbon::parse($event->start_event)->isoFormat('dddd, DD MMMM YYYY') }}</h5>
								<h6 class="font-weight-normal">{{ \Carbon\Carbon::parse($event->start_event)->isoFormat('hh:mm a') . ' - ' . \Carbon\Carbon::parse($event->end_event)->isoFormat('hh:mm a') }}
								</h6>
							</div>
							<div style="padding: 0 1px; background-color: {{ $event->color_text_event }}"></div>
							<div class="mx-3 text-left">
								<h5>{{ $event->place_event }}</h5>
								<h6 class="font-weight-normal">{{ $event->location_event }}</h6>
							</div>
						</div>

						<h5 class="pt-2" style="margin:5px 0 0 0;">
							{{ strtoupper($invt->type_invitation) }}
							{{ $invt->table_number_invitation != null ? '- '.ucwords($invt->table_number_invitation) : '' }}
						</h5>
						<div>
							{{ $invt->information_invitation }}
						</div>

						<!-- Bagian QR Code yang ingin dihilangkan -->
						<div class="text-center mt-4">
							@php
								$qrImagePath = '';
								$customQrEnabled = isset(mySetting()->enable_custom_qr) && mySetting()->enable_custom_qr == 1;
								$templateId = $customQrEnabled ? ($invt->custom_qr_template_id ?? null) : null;
								$qrData = $invt->qrcode_invitation;
								
								// Only use custom QR path if feature is enabled
								if ($customQrEnabled && $invt->custom_qr_path) {
									$qrImagePath = str_replace('public/', 'storage/', $invt->custom_qr_path);
								} else {
									$qrImagePath = '/img/qrCode/' . $invt->qrcode_invitation . '.png';
								}
							@endphp
							
							<div id="qrcode-container" style="width: 300px; height: 300px; margin: 0 auto;"></div>
							<h5 class="mt-3">
								<div id="qrcode-id" class="h6" style="cursor:pointer">
									<span>
										{{ $invt->qrcode_invitation }}
									</span>
									<i class="far fa-copy"></i>
								</div>
							</h5>

							<a class="shadow-none btn rounded-pill btn-warning my-2"
								href="{{ url('download/' . $invt->qrcode_invitation) }}">Download QrCode</a>
                            
                            @if (mySetting()->enable_rsvp == 1)
                            <a class="shadow-none btn rounded-pill btn-info my-2 ml-2"
                                href="{{ route('rsvp.guestForm', ['qrcode' => $invt->qrcode_invitation]) }}">Respond to RSVP</a>
                            @endif
						</div>
						<!-- Akhir bagian QR Code -->

						<p class="py-2" style="font-size:13px;"><i>* Simpan barcode dan tunjukkan pada saat acara.</i></p>

						<p class="mt-3">{!! nl2br($event->information_event) !!}</p>

					</div>

					<div class="text-center mt-5">
						@if ($event->image_left_event != '' && $event->image_left_status == 1)
						<img src="{{ asset('img/event/' . $event->image_left_event) }}" style="width:240px; border: 10px solid #eee; transform: rotate(-10deg);">
						@endif
					</div>
					<div class="text-center mt-2">
						@if ($event->image_right_event != '' && $event->image_right_status == 1)
						<img src="{{ asset('img/event/' . $event->image_right_event) }}" style="width:240px; border: 10px solid #eee; transform: rotate(-10deg);">
						@endif
					</div>
				</div>

				{{-- <div class="col-sm-3 d-none d-lg-block"></div> --}}

            </div>

			</div>
        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('template/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('template/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/node_modules/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/stisla.js') }}"></script>
    <!-- JS Libraies -->
    <script src="{{ asset('template/node_modules/izitoast/dist/js/iziToast.min.js') }}"></script>
    <script src="{{ asset('plugin/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <!-- Template JS File -->
    <script src="{{ asset('template/assets/js/scripts.js') }}"></script>
    
    <!-- QR Code Styling library -->
    <script src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>

    <script>
        $(document).ready(function() {
            $('#qrcode-id').click(function() {
                let textToCopy = $('#qrcode-id span').text();
                let tempTextarea = $('<textarea>');
                $('body').append(tempTextarea);
                tempTextarea.val(textToCopy).select();
                document.execCommand('copy');
                tempTextarea.remove();
                iziToast.success({
                    title: 'Berhasil',
                    message: "Qrcode berhasil dicopy",
                    position: 'bottomCenter'
                });
            });

            let fromRegister = "{{ session()->get('register-success') }}";
            if (fromRegister) {
                Swal.fire({
                    title: "Registrasi Berhasil",
                    text: fromRegister,
                    icon: "success",
                    confirmButtonColor: "#6F4E37",
                });
            }
            
            // Initialize QR code with styling
            const templateId = {{ $templateId ?? 'null' }};
            const qrData = "{{ $qrData }}";
            const customQrEnabled = {{ $customQrEnabled ? 'true' : 'false' }};
            
            if (customQrEnabled && templateId) {
                // Fetch template settings from API
                fetch(`/api/custom-qr/${templateId}/preview?data=${encodeURIComponent(qrData)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.template && data.template.settings) {
                            // Create QR with the template settings
                            const settings = data.template.settings;
                            
                            // Match Endroid dimensions - use 300x300 size
                            settings.width = 300;
                            settings.height = 300;
                            
                            // Match Endroid margin of 10
                            settings.margin = 10;
                            
                            // Ensure errorCorrectionLevel is set to H like Endroid
                            if (settings.qrOptions) {
                                settings.qrOptions.errorCorrectionLevel = 'H';
                            }
                            
                            const qrCode = new QRCodeStyling(settings);
                            qrCode.append(document.getElementById("qrcode-container"));
                        } else {
                            // Fallback - show the static image if API fails
                            fallbackToStaticImage();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching QR template:', error);
                        fallbackToStaticImage();
                    });
            } else {
                fallbackToStaticImage();
            }
            
            function fallbackToStaticImage() {
                // Create fallback image
                const qrImagePath = "{{ asset($qrImagePath) }}";
                const container = document.getElementById("qrcode-container");
                container.innerHTML = `<img src="${qrImagePath}" class="rounded" style="width: 100%; height: 100%; object-fit: contain;" alt="QR Code">`;
            }
        });
    </script>

</body>
</html>
