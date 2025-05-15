<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title>
    
    <!-- General CSS Files - Using CDNs for reliability -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- CSS Libraries - Using CDNs -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chocolat@1.0.0/dist/css/chocolat.min.css">
    
    <!-- Template CSS - Use local files that we know exist -->
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/custom.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ mySetting()->logo_app != '' ? asset('img/app/'.mySetting()->logo_app) : asset('template/assets/img/logo.png') }}">
</head>

<body>
    <!-- Include jQuery first - from CDN with local fallback -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="{{ asset("template/node_modules/jquery/dist/jquery.min.js") }}"><\/script>');
    </script>

    <div id="app">
        <div class="main-wrapper">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <form class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                    </ul>
                </form>
                <ul class="navbar-nav navbar-right">
                    <li class="dropdown">
						<a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <img alt="image" src="{{ asset('template/assets/img/avatar/avatar-4.png') }}" class="rounded-circle mr-1">
                            <div class="d-sm-none d-lg-inline-block">{{ auth()->user()->name }}</div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="{{ url('user-profile') }}" class="dropdown-item has-icon">
                                <i class="far fa-user"></i> Profile
                            </a>
                            {{-- <a href="{{ url('change-password') }}" class="dropdown-item has-icon"><i class="fas fa-cog"></i> Ubah Password</a> --}}
                            <div class="dropdown-divider"></div>
                            <a href="{{ url('logout') }}" class="dropdown-item has-icon text-danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand">
                        <a href="{{ url('/dashboard') }}">
    
                            {{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}
                        </a>
                    </div>
                    <div class="sidebar-brand sidebar-brand-sm">
                        <a href="{{ url('/dashboard') }}"><i class="fa fa-paper-plane"></i></a>
                    </div>
                    <ul class="sidebar-menu">

                        <li class="menu-header">Dashboard</li>
                        <li class="{{ request()->segment(1) == 'dashboard' ? 'active' : '' }}">
							<a class="nav-link" href="{{ url('/dashboard') }}">
								<i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
							</a>
						</li>
                        <li class="menu-header">Menu</li>
                        <li class="{{ request()->segment(1) == 'register' ? 'active' : '' }}">
							<a class="nav-link" href="{{ url('register') }}">
								<i class="fas fa-address-card"></i> <span>Register Tamu</span>
							</a>
						</li>
                        <li>
							<a class="nav-link" href="{{ url('doorprize') }}">
								<i class="fas fa-address-card"></i> <span>Doorprize</span>
							</a>
						</li>
                        
                        @if (auth()->user()->role == 1)
                            <li class="{{ request()->segment(1) == 'invite' ? 'active' : '' }}">
								<a class="nav-link" href="{{ url('invite') }}">
									<i class="fas fa-envelope"></i> <span>Undangan</span>
								</a>
							</li>
                        @endif
							                 <li class="{{ request()->segment(1) == 'blasting' ? 'active' : '' }}">
							                     <a class="nav-link" href="{{ url('blasting') }}">
							                         <i class="fas fa-bullhorn"></i> <span>Blasting</span>
							                     </a>
							                 </li>
                        
                        @if (mySetting()->enable_rsvp == 1)
                        <li class="{{ request()->segment(1) == 'rsvp' ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('rsvp') }}">
                                <i class="fas fa-reply"></i> <span>RSVP</span>
                            </a>
                        </li>
                        @endif
                        
                        @if (isset(mySetting()->enable_custom_qr) && mySetting()->enable_custom_qr == 1)
                        <li class="{{ request()->segment(1) == 'custom-qr' ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('custom-qr') }}">
                                <i class="fas fa-qrcode"></i> <span>Custom QR Design</span>
                            </a>
                        </li>
                        @endif
                        
                        <li class="menu-header">LOGS</li>
                        <li class="{{ request()->segment(1) == 'arrival-log' ? 'active' : '' }}">
							<a class="nav-link" href="{{ url('arrival-log') }}">
								<i class="fas fa-list-ul"></i> <span>Log Kedatangan</span>
							</a>
						</li>
                        <li class="{{ request()->segment(1) == 'souvenir' && request()->segment(2) == 'logs' ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('souvenir/logs') }}">
                                <i class="fas fa-clipboard-list"></i> <span>Log Souvenir</span>
                            </a>
                        </li>
                        
						<li class="menu-header">Setting</li>
							                 @if (auth()->user()->role == 1)
						<li class="{{ request()->segment(1) == 'event' ? 'active' : '' }}">
							<a class="nav-link" href="{{ url('event') }}">
								<i class="fas fa-calendar-check"></i> <span>Acara</span>
							</a>
						</li>
						<li class="{{ request()->segment(1) == 'setting' ? 'active' : '' }}">
							<a class="nav-link" href="{{ url('setting') }}">
								<i class="fas fa-cog"></i> <span>Aplikasi</span>
							</a>
						</li>
                        <li class="{{ request()->segment(1) == 'user' ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('user') }}">
                                <i class="fas fa-user"></i> <span>User</span>
                            </a>
                        </li>
						@endif
                        <li class="menu-header">Scan</li>
                        {{-- <li class="nav-item dropdown">
                            <a href="#" class="nav-link has-dropdown"><i class="fas fa-qrcode"></i><span>Proses Scan</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" target="_blank" href="{{ url('scan/in') }}">Scan In</a></li>
                                <li><a class="nav-link" target="_blank" href="{{ url('scan/out') }}">Scan Out</a></li>
                            </ul>
                        </li> --}}
						<li>
							<a class="nav-link" href="{{ url('scan/in') }}" target="_blank">
								<i class="fas fa-qrcode"></i> <span>Scan In</span>
							</a>
						</li>
						<li>
							<a class="nav-link" href="{{ url('scan/out') }}" target="_blank">
								<i class="fas fa-qrcode"></i> <span>Scan Out</span>
							</a>
						</li>
                        <li>
                            <a class="nav-link" href="{{ url('souvenir/scan') }}" target="_blank">
                                <i class="fas fa-gift"></i> <span>Scan Souvenir</span>
                            </a>
                        </li>
                        <li>
							<a class="nav-link" href="{{ url('scan/greeting') }}" target="_blank">
								<i class="fas fa-handshake"></i> <span>Greeting</span>
							</a>
						</li>
                        <li class="{{ request()->segment(1) == 'arrived-manually' ? 'active' : '' }}">
							<a class="nav-link" href="{{ url('arrived-manually') }}">
								<i class="fas fa-pencil-alt"></i> <span>Scan Manual</span>
							</a>
						</li>

                    </ul>
                </aside>
            </div>

            @yield('content')

            <footer class="main-footer">
                <div class="footer-left">
                    © 2024 Made with love by Pramuji. Powered by YukCoding Dev.</a>
                </div>
                <div class="footer-right">v2.1.3</div>
            </footer>
        </div>
    </div>

    <!-- General JS Scripts - Using CDNs -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="{{ asset('template/assets/js/stisla.js') }}"></script>
    
    <!-- JS Libraries - Using CDNs -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    
    <!-- Template JS File -->
    <script src="{{ asset('template/assets/js/scripts.js') }}"></script>
    <script src="{{ asset('template/assets/js/custom.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            toast("{{ session()->get('success') }}", "{{ session()->get('warning') }}",
                "{{ session()->get('error') }}");
        });
    </script>
    
    @stack('scripts')
</body>
</html>
