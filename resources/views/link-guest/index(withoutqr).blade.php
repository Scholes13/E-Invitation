<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

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
    <style>
		.popup-overlay {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.7);
			display: flex;
			justify-content: center;
			align-items: center;
			visibility: hidden;
			opacity: 0;
			transition: visibility 0.3s, opacity 0.3s;
			z-index: 1000;
		}
	
		.popup-content {
			background: rgb(119, 5, 5);
			padding: 20px;
			border-radius: 10px;
			text-align: center;
			position: relative;
		}
	
		.popup-close {
			position: absolute;
			top: 10px;
			right: 10px;
			font-size: 20px;
			cursor: pointer;
		}
	
		.popup-overlay.active {
			visibility: visible;
			opacity: 1;
		}
		.button-container {
    display: flex;
    gap: 10px; /* Adds space between the buttons */
    justify-content: center; /* Centers the buttons horizontally */
    margin-top: 20px;  /* Adds space above the container */
    margin-bottom: 20px; /* Adds space below the container */
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
							<img src="{{ $img }}" style="max-width: 50%; width: 100px; height: auto; border: 0px solid #eee; margin-top:5px" alt="">
						@endif
						</div>
						<div class="mt-3 text-center">
							<div class="h2" style="margin-top:25px">
								{!! nl2br($event->name_event) !!}
							</div>
							
							<div style="margin:25px 0 20px 0;">
								
								<div class="h5 mb-50px">
									<div>Hello, {{ $invt->name_guest }} ! </div>
								</div>
								<div>
									<i>Treat your hair and eyes with some extra love today. </i>
								</div>
									<div>
										<i>Enjoy these gifts from <b>Gooper</b>!</i>
								</div>
				
								<div style="margin-top: 20px;">
									<i>Thank you team for still being enthusiastic until this day. </i>
								</div>
								<div>
									<i>don't forget to bring the clothes that we have provided.</i>
								</div>
							</div>
						</div>
		
						<div class="additional-text text-center" style="margin-top: 20px;">
    <h6>Support this event by calculating your carbon emissions</h2>
    <div>Let's play our part in caring for the planet by making eco-friendly </div> <div>choices and supporting reforestation efforts. </div>
<div class="button-container">
    <a href="https://werkudara.jejakin.app" class="btn btn-primary floating-btn">
        Jejakin
    </a>
    <a href="https://wam.maharajapratama.com/public/ehandbook/room.pdf" 
   class="btn btn-primary floating-btn" 
   download="room.pdf">
   Download Room List
</a>
</div>

<style>
    /* Styling dasar tombol */
    .floating-btn {
		margin-top: 50px;
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #245587;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* Efek hover */
    .floating-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }

    /* Efek mengambang */
    @keyframes floating {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-8px);
        }
    }

    .floating-btn {
        animation: floating 2s infinite ease-in-out;
    }
</style>
					
						<div>
							{{ $invt->information_invitation }}
						</div>

						

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

    <script>
        $(document).ready(function() {
            $('#qrcode').click(function() {
                let textToCopy = $('#qrcode span').text();
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
        });
    </script>
    
   <!-- Pemutar Audio (Tanpa Kontrol) -->
<audio id="myAudio" autoplay>
    <source src="https://wam.maharajapratama.com/public/music/lagubaru.mp3" type="audio/mpeg">
    Your browser does not support the audio tag.
</audio>

<!-- Tombol Play/Pause Kustom -->
<button id="playPauseButton">Play</button>

<script>
    // Ambil elemen audio dan tombol play/pause
    const audio = document.getElementById('myAudio');
    const playPauseButton = document.getElementById('playPauseButton');

    // Fungsi untuk toggle play/pause
    playPauseButton.addEventListener('click', function() {
        if (audio.paused) {
            audio.play();  // Jika audio pause, play audio
            playPauseButton.textContent = 'Pause';  // Ubah teks tombol menjadi "Pause"
        } else {
            audio.pause();  // Jika audio sedang bermain, pause audio
            playPauseButton.textContent = 'Play';  // Ubah teks tombol menjadi "Play"
        }
    });
</script>

<script>
    // Fungsi untuk menutup pop-up
    function closePopup() {
        document.getElementById('popup').classList.remove('active');
    }

    // Contoh: Tampilkan pop-up secara otomatis setelah halaman dimuat
    window.onload = function () {
        document.getElementById('popup').classList.add('active');
    };
</script>


</body>
</html>
