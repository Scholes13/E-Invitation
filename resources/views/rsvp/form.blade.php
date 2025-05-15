<!--
 * RSVP Form Template
 * Version: 2.2.1
 * Last Updated: {{ date('Y-m-d') }}
 * 
 * Modern and responsive RSVP form with automatic guest management
 -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RSVP - {{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/components.css') }}">
    
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/all.min.css') }}">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ mySetting()->logo_app != '' ? asset('img/app/'.mySetting()->logo_app) : asset('template/assets/img/logo.png') }}">
    
    <style>
        :root {
            --primary-color: {{ mySetting()->color_bg_app ?? '#6c3c0c' }};
            --primary-hover: {{ mySetting()->color_bg_app ? 'brightness(85%)' : '#5a320a' }};
            --border-radius: 18px;
            --box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            --box-shadow-hover: 0 10px 30px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }
        
        body {
            background-color: var(--primary-color);
            @if (mySetting()->image_bg_app != '' && mySetting()->image_bg_status == 1)
                background-image: url("{{ asset('img/app/' . mySetting()->image_bg_app) }}");
            @endif
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            font-family: 'Poppins', 'Nunito', 'Segoe UI', sans-serif;
        }
        
        /* Container sizing adjustments */
        .container {
            max-width: 1200px; /* Wider container */
            width: 100%;
            padding: 0 15px;
        }
        
        .main-card {
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 100%;
            margin: 0 auto;
            overflow: hidden;
            background-color: #fff;
            position: relative;
            /* Width based on screen sizes */
            max-width: 90%; 
        }
        
        /* Width adjustments for different screen sizes */
        @media (min-width: 576px) {
            .main-card {
                max-width: 90%;
            }
        }
        
        @media (min-width: 768px) {
            .main-card {
                max-width: 85%;
            }
        }
        
        @media (min-width: 992px) {
            .main-card {
                max-width: 75%;
            }
        }
        
        @media (min-width: 1200px) {
            .main-card {
                max-width: 65%;
            }
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f2f2f2;
            padding: 25px 30px;
            text-align: center;
        }
        
        .card-body {
            padding: 35px;
        }
        
        @media (max-width: 768px) {
            .card-body {
                padding: 25px 20px;
            }
        }
        
        /* Modern logo container */
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .logo-container img {
            max-width: 120px;
            height: auto;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }
        
        .logo-container img:hover {
            transform: scale(1.05);
        }
        
        .logo-container h1 {
            font-weight: 700;
            color: #333;
            font-size: 26px;
            letter-spacing: 0.5px;
        }
        
        /* Styled text elements */
        .guest-name {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .text-muted {
            color: #6c757d !important;
            font-size: 16px;
            font-weight: 400;
        }
        
        @media (max-width: 576px) {
            .guest-name {
                font-size: 26px;
            }
            
            .text-muted {
                font-size: 14px;
            }
        }
        
        /* Modern card containers */
        .card-container {
            background-color: #f9f9fc;
            padding: 28px;
            border-radius: var(--border-radius);
            margin-bottom: 28px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            border-left: 4px solid var(--primary-color);
        }
        
        .card-container:hover {
            box-shadow: var(--box-shadow-hover);
            transform: translateY(-2px);
        }
        
        @media (max-width: 576px) {
            .card-container {
                padding: 20px 18px;
            }
        }
        
        /* Event details styling */
        .event-details {
            position: relative;
        }
        
        .event-details i {
            width: 24px;
            color: var(--primary-color);
            margin-right: 12px;
            font-size: 18px;
        }
        
        .event-details p {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            font-size: 15px;
        }
        
        .event-details strong {
            color: #444;
            min-width: 70px;
            display: inline-block;
            font-weight: 600;
        }
        
        /* Row adjustments in event details */
        .event-details .row {
            margin-left: -10px;
            margin-right: -10px;
        }
        
        .event-details .col-md-6 {
            padding-left: 10px;
            padding-right: 10px;
        }
        
        /* Attendance options styling */
        .attendance-option {
            position: relative;
            margin-bottom: 16px;
        }
        
        .attendance-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .attendance-option label {
            display: flex;
            align-items: center;
            background-color: #fff;
            padding: 18px 22px;
            border-radius: var(--border-radius);
            border: 2px solid #e9ecef;
            transition: var(--transition);
            cursor: pointer;
            font-weight: 600;
            margin: 0;
            color: #495057;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        
        .attendance-option input[type="radio"]:checked + label {
            background-color: #e8f4fc;
            border-color: #4d8efc;
            box-shadow: 0 3px 10px rgba(77, 142, 252, 0.15);
        }
        
        .attendance-option.yes input[type="radio"]:checked + label {
            background-color: #e3f7e8;
            border-color: #28a745;
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.15);
        }
        
        .attendance-option.no input[type="radio"]:checked + label {
            background-color: #fbe7e9;
            border-color: #dc3545;
            box-shadow: 0 3px 10px rgba(220, 53, 69, 0.15);
        }
        
        .attendance-option.maybe input[type="radio"]:checked + label {
            background-color: #fff8e6;
            border-color: #ffc107;
            box-shadow: 0 3px 10px rgba(255, 193, 7, 0.15);
        }
        
        .attendance-option label i {
            font-size: 20px;
            margin-right: 14px;
        }
        
        /* Section titles */
        .section-title {
            position: relative;
            padding-bottom: 14px;
            margin-bottom: 24px;
            font-weight: 600;
            color: #333;
            font-size: 20px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
            border-radius: 3px;
        }
        
        /* Form elements */
        .form-group label {
            font-weight: 600;
            color: #444;
            margin-bottom: 10px;
            font-size: 15px;
        }
        
        .form-control {
            border-radius: 12px;
            padding: 12px 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: var(--transition);
            font-size: 15px;
            background-color: #fff;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(108, 60, 12, 0.1);
        }
        
        /* Custom select styling */
        .select-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .select-wrapper::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
            font-size: 18px;
        }
        
        .select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            padding-right: 40px;
            cursor: pointer;
            background-color: white;
            color: #444;
            font-weight: 500;
        }
        
        /* Guest names field */
        .guest-names-wrapper {
            margin-top: 15px;
            transition: var(--transition);
            overflow: hidden;
            max-height: 0;
            opacity: 0;
        }
        
        .guest-names-wrapper.active {
            max-height: 500px;
            opacity: 1;
            margin-top: 20px;
        }
        
        /* Submit button */
        .submit-container {
            text-align: center;
            margin-top: 40px;
        }
        
        .submit-btn {
            padding: 14px 45px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 576px) {
            .submit-btn {
                width: 100%;
                padding: 14px 20px;
            }
        }
        
        /* Small text styles */
        .form-text {
            font-size: 13px;
            color: #6c757d;
            margin-top: 6px;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animated {
            animation: fadeIn 0.6s ease forwards;
        }
        
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        
        /* Error messages */
        .text-danger {
            color: #dc3545 !important;
            font-size: 13px;
            margin-top: 6px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-card animated">
            <div class="card-body">
                <div class="logo-container">
                    <img src="{{ mySetting()->logo_app != '' ? asset('img/app/'.mySetting()->logo_app) : asset('template/assets/img/logo.png') }}" alt="Logo" class="mb-3">
                    <h1 class="h3">{{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</h1>
                </div>

                <div class="text-center mb-4 animated delay-1">
                    <h2 class="guest-name">{{ $invitation->name_guest }}</h2>
                    @if($setting->rsvp_deadline)
                    <p class="text-muted">We kindly request your response by: {{ date('F j, Y', strtotime($setting->rsvp_deadline)) }}</p>
                    @else
                    <p class="text-muted">We look forward to your response</p>
                    @endif
                </div>

                <div class="card-container event-details animated delay-2">
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-calendar-alt"></i> <strong>Date:</strong> 
                                {{ \App\Models\Event::first() ? date('F j, Y', strtotime(\App\Models\Event::first()->start_event)) : 'Event date' }}
                            </p>
                            <p><i class="fas fa-clock"></i> <strong>Time:</strong> 
                                {{ \App\Models\Event::first() ? date('g:i A', strtotime(\App\Models\Event::first()->start_event)) : 'Event time' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> 
                                {{ \App\Models\Event::first() ? \App\Models\Event::first()->place_event : 'Event location' }}
                            </p>
                            <p><i class="fas fa-info-circle"></i> <strong>Type:</strong> 
                                {{ $invitation->type_invitation }}
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('rsvp.process', ['qrcode' => $invitation->qrcode_invitation]) }}" method="POST" id="rsvpForm">
                    @csrf
                    
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="card-container animated delay-3">
                        <h4 class="section-title">Will you attend?</h4>
                        
                        <div class="attendance-options">
                            <div class="attendance-option yes">
                                <input type="radio" name="rsvp_status" id="rsvp_yes" value="yes" {{ (old('rsvp_status', $invitation->rsvp_status) == 'yes') ? 'checked' : '' }}>
                                <label for="rsvp_yes">
                                    <i class="fas fa-check-circle text-success"></i> Yes, I will attend
                                </label>
                            </div>
                            
                            <div class="attendance-option no">
                                <input type="radio" name="rsvp_status" id="rsvp_no" value="no" {{ (old('rsvp_status', $invitation->rsvp_status) == 'no') ? 'checked' : '' }}>
                                <label for="rsvp_no">
                                    <i class="fas fa-times-circle text-danger"></i> No, I am unable to attend
                                </label>
                            </div>
                            
                            <div class="attendance-option maybe">
                                <input type="radio" name="rsvp_status" id="rsvp_maybe" value="maybe" {{ (old('rsvp_status', $invitation->rsvp_status) == 'maybe') ? 'checked' : '' }}>
                                <label for="rsvp_maybe">
                                    <i class="fas fa-question-circle text-warning"></i> Maybe, I'm not sure yet
                                </label>
                            </div>
                        </div>
                        
                        @error('rsvp_status')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="attendance-details" class="animated delay-4 {{ (old('rsvp_status', $invitation->rsvp_status) != 'yes') ? 'd-none' : '' }}">
                        @if($setting->enable_plus_ones)
                        <div class="card-container">
                            <h4 class="section-title">Guest Information</h4>
                            <div class="form-group">
                                <label for="plus_ones_count">How many guests are you bringing?</label>
                                <div class="select-wrapper">
                                    <select class="form-control" id="plus_ones_count" name="plus_ones_count">
                                        <option value="0" @if(old('plus_ones_count', $invitation->plus_ones_count) == 0) selected @endif>Just me</option>
                                        <option value="1" @if(old('plus_ones_count', $invitation->plus_ones_count) == 1) selected @endif>1 guest</option>
                                        <option value="2" @if(old('plus_ones_count', $invitation->plus_ones_count) == 2) selected @endif>2 guests</option>
                                        <option value="3" @if(old('plus_ones_count', $invitation->plus_ones_count) == 3) selected @endif>3 guests</option>
                                        <option value="4" @if(old('plus_ones_count', $invitation->plus_ones_count) == 4) selected @endif>4 guests</option>
                                        <option value="5" @if(old('plus_ones_count', $invitation->plus_ones_count) == 5) selected @endif>5 guests</option>
                                    </select>
                                </div>
                                @error('plus_ones_count')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="guest-names-wrapper" class="guest-names-wrapper {{ (old('plus_ones_count', $invitation->plus_ones_count) > 0) ? 'active' : '' }}">
                                <div class="form-group mb-0">
                                    <label for="plus_ones_names">Names of additional guests</label>
                                    <input type="text" class="form-control" id="plus_ones_names" name="plus_ones_names" placeholder="e.g. John Smith, Jane Doe" value="{{ old('plus_ones_names', $invitation->plus_ones_names) }}">
                                    <small class="form-text text-muted">For name tags and seating arrangements</small>
                                    @error('plus_ones_names')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($setting->collect_dietary_preferences)
                        <div class="card-container">
                            <h4 class="section-title">Dietary Information</h4>
                            <div class="form-group mb-0">
                                <label for="dietary_preferences">Dietary Preferences or Restrictions</label>
                                <textarea class="form-control" id="dietary_preferences" name="dietary_preferences" rows="3" placeholder="Vegetarian, vegan, allergies, etc.">{{ old('dietary_preferences', $invitation->dietary_preferences) }}</textarea>
                                @error('dietary_preferences')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="submit-container animated delay-4">
                        <button type="submit" class="btn btn-primary submit-btn">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Response
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('template/node_modules/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('template/node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    
    <script>
        $(document).ready(function() {            
            // Handle radio buttons for attendance
            $('input[name="rsvp_status"]').change(function() {
                var value = $(this).val();
                
                // Show/hide additional options for 'yes' response
                if (value === 'yes') {
                    $('#attendance-details').removeClass('d-none').addClass('animated');
                } else {
                    $('#attendance-details').addClass('d-none');
                }
            });
            
            // Handle guest count change
            $('#plus_ones_count').change(function() {
                var count = parseInt($(this).val());
                if (count > 0) {
                    $('#guest-names-wrapper').addClass('active');
                } else {
                    $('#guest-names-wrapper').removeClass('active');
                }
            });
            
            // Form validation
            $('#rsvpForm').on('submit', function(e) {
                let isValid = true;
                let rsvpStatus = $('input[name="rsvp_status"]:checked').val();
                
                if (!rsvpStatus) {
                    alert('Please select whether you will attend or not.');
                    isValid = false;
                    return false;
                }
                
                // Validate guest names if bringing guests
                if (rsvpStatus === 'yes') {
                    let guestCount = parseInt($('#plus_ones_count').val());
                    let guestNames = $('#plus_ones_names').val().trim();
                    
                    if (guestCount > 0 && guestNames === '') {
                        alert('Please provide the names of your guests.');
                        $('#plus_ones_names').focus();
                        isValid = false;
                        return false;
                    }
                }
                
                return isValid;
            });
        });
    </script>
    <div class="text-center mt-3">
        <small class="text-muted" style="font-size: 10px; opacity: 0.5;">v2.1.3</small>
    </div>
</body>
</html> 