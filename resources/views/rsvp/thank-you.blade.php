<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - {{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/components.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ mySetting()->logo_app != '' ? asset('img/app/'.mySetting()->logo_app) : asset('template/assets/img/logo.png') }}">
    <style>
        body {
            background-color: {{ mySetting()->color_bg_app ?? '#6c3c0c' }};
            @if (mySetting()->image_bg_app != '' && mySetting()->image_bg_status == 1)
                background-image: url("{{ asset('img/app/' . mySetting()->image_bg_app) }}");
            @endif
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            text-align: center;
        }
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            background-color: #fff;
            border-bottom: 1px solid #f2f2f2;
            padding: 20px;
        }
        .card-body {
            padding: 40px;
        }
        .logo-container {
            margin-bottom: 30px;
        }
        .logo-container img {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .thank-you-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn-custom-primary {
            background-color: {{ mySetting()->color_bg_app ?? '#6c3c0c' }};
            border-color: {{ mySetting()->color_bg_app ?? '#6c3c0c' }};
            color: #fff;
            margin-top: 20px;
        }
        .btn-custom-primary:hover {
            filter: brightness(90%);
            color: #fff;
        }
        .response-details {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <div class="logo-container">
                    <img src="{{ mySetting()->logo_app != '' ? asset('img/app/'.mySetting()->logo_app) : asset('template/assets/img/logo.png') }}" alt="Logo" class="mb-3">
                    <h1 class="h3">{{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</h1>
                </div>

                <i class="fas fa-check-circle thank-you-icon"></i>
                <h2>Thank You!</h2>
                <p class="lead">We've received your RSVP response.</p>

                <div class="response-details">
                    <p><strong>Name:</strong> {{ $invitation->name_guest }}</p>
                    <p>
                        <strong>Response:</strong> 
                        @if($invitation->rsvp_status == 'yes')
                            <span class="text-success">Attending</span>
                        @elseif($invitation->rsvp_status == 'no')
                            <span class="text-danger">Not Attending</span>
                        @elseif($invitation->rsvp_status == 'maybe')
                            <span class="text-info">Maybe</span>
                        @endif
                    </p>
                    
                    @if($invitation->rsvp_status == 'yes')
                        @if($invitation->plus_ones_count > 0)
                            <p><strong>Additional Guests:</strong> {{ $invitation->plus_ones_count }}</p>
                            @if($invitation->plus_ones_names)
                                <p><strong>Guest Names:</strong> {{ $invitation->plus_ones_names }}</p>
                            @endif
                        @endif
                        
                        @if($invitation->dietary_preferences)
                            <p><strong>Dietary Preferences:</strong> {{ $invitation->dietary_preferences }}</p>
                        @endif
                    @endif
                    
                    @if($invitation->rsvp_notes)
                        <p><strong>Notes:</strong> {{ $invitation->rsvp_notes }}</p>
                    @endif
                </div>

                <p>You can update your response at any time before the deadline by visiting the RSVP link again.</p>
                
                <a href="{{ url('/') }}" class="btn btn-custom-primary">Return to Homepage</a>
            </div>
        </div>
    </div>
</body>
</html> 