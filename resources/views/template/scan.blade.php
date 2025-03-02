<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    {{-- <title>{{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title> --}}
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('template/node_modules/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/node_modules/@fortawesome/fontawesome-free/css/all.css') }}">
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('plugin/sweetalert2/dist/sweetalert2.min.css') }}">
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/components.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ mySetting()->logo_app != '' ? asset('img/app/' . mySetting()->logo_app) : asset('template/assets/img/logo.png') }}">
    <style>
        .custom-bg {
            @if (mySetting()->image_bg_app != '' && mySetting()->image_bg_status == 1)
                background-image: url("{{ asset('img/app/' . mySetting()->image_bg_app) }}");
                background-size: cover;
                background-position: center;
            @endif
            background-color: {{ mySetting()->color_bg_app ?? '#6c3c0c' }};
        }
    </style>
</head>

<body class="custom-bg">
    <script src="{{ asset('template/node_modules/jquery/dist/jquery.min.js') }}"></script>

    <div id="app">
        <div class="main-wrapper">

            @yield('content')

        </div>
    </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('template/node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('template/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('template/node_modules/jquery.nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('template/assets/js/stisla.js') }}"></script>
    <!-- JS Libraies -->
    <script src="{{ asset('plugin/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <!-- Template JS File -->
    <script src="{{ asset('template/assets/js/scripts.js') }}"></script>
    {{-- <script src="{{ asset('template/assets/js/custom.js') }}"></script> --}}

</body>

</html>
