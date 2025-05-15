@extends('template.template')

@section('content')
<!-- Add SweetAlert2 CSS -->
<link rel="stylesheet" href="{{ asset('node_modules/sweetalert2/dist/sweetalert2.min.css') }}">
<!-- Fallback to CDN if node_modules isn't available -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if the SweetAlert2 CSS was loaded
        let styleLoaded = false;
        for (let i = 0; i < document.styleSheets.length; i++) {
            if (document.styleSheets[i].href && document.styleSheets[i].href.includes('sweetalert2.min.css')) {
                styleLoaded = true;
                break;
            }
        }
        if (!styleLoaded) {
            console.log('Loading SweetAlert2 CSS from CDN');
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css';
            document.head.appendChild(link);
        }
        
        // Verify CSRF token exists
        if (!document.querySelector('meta[name="csrf-token"]')) {
            console.error('CSRF token meta tag not found! Adding one dynamically');
            const metaTag = document.createElement('meta');
            metaTag.name = 'csrf-token';
            metaTag.content = '{{ csrf_token() }}';
            document.head.appendChild(metaTag);
        }
    });
</script>

<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>QR Template Designer</h1>
        </div>
        
        <div class="row">
            <div class="col-12 mb-4">
                <div class="hero bg-primary text-white">
                    <div class="hero-inner">
                        <h2>Design QR Template</h2>
                        <p class="lead">Create beautiful custom QR code templates with perfect logo placement and extensive styling options.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h4>QR Design Options</h4>
                        <div class="card-header-action">
                            <div class="btn-group">
                                <button id="saveTemplateBtn" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i> Save Template
                                </button>
                                <button id="loadTemplateBtn" class="btn btn-info">
                                    <i class="fas fa-folder-open mr-1"></i> Load Template
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="qrGeneratorForm">
                            <!-- Basic Options -->
                            <div class="form-group">
                                <label for="qrData">QR Content</label>
                                <input type="text" class="form-control" id="qrData" value="https://example.com/sample-qr-code">
                                <small class="form-text text-muted">The URL or text to encode in the QR code</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="logoUpload">Logo Image</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logoUpload" accept="image/*">
                                    <label class="custom-file-label" for="logoUpload">Choose logo</label>
                                </div>
                                <small class="form-text text-muted">For best results, use a square image with transparent background</small>
                                
                                @if(isset($selectedTemplate) && $selectedTemplate->logo_path)
                                <div class="mt-2">
                                    <p class="mb-1">Current logo:</p>
                                    <img src="{{ Storage::url(str_replace('public/', '', $selectedTemplate->logo_path)) }}" 
                                        alt="Current logo" class="img-thumbnail" style="max-height: 100px; max-width: 100px;">
                                    <div class="form-text text-info">
                                        <small>Logo will be preserved unless you select a new one.</small>
                                    </div>
                                </div>
                                @endif
                                
                                <!-- Hidden field to store template ID when editing -->
                                @if(isset($selectedTemplate))
                                <input type="hidden" id="templateId" value="{{ $selectedTemplate->id }}">
                                @else
                                <input type="hidden" id="templateId" value="">
                                @endif
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Width (px)</label>
                                        <input type="number" class="form-control" id="qrWidth" value="300" min="100" max="1000">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Height (px)</label>
                                        <input type="number" class="form-control" id="qrHeight" value="300" min="100" max="1000">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Margin (px)</label>
                                <input type="number" class="form-control" id="qrMargin" value="10" min="0" max="50">
                            </div>
                            
                            <!-- Tabs for different options -->
                            <ul class="nav nav-tabs" id="optionsTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="dots-tab" data-toggle="tab" href="#dots" role="tab">Dots</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="corners-tab" data-toggle="tab" href="#corners" role="tab">Corners</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="corner-dots-tab" data-toggle="tab" href="#corner-dots" role="tab">Corner Dots</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="background-tab" data-toggle="tab" href="#background" role="tab">Background</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="logo-tab" data-toggle="tab" href="#logo" role="tab">Logo</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="advanced-tab" data-toggle="tab" href="#advanced" role="tab">Advanced</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="templates-tab" data-toggle="tab" href="#templates" role="tab">Templates</a>
                                </li>
                            </ul>
                            
                            <div class="tab-content pt-4" id="optionsTabContent">
                                <!-- Dots Options -->
                                <div class="tab-pane fade show active" id="dots" role="tabpanel">
                                    <div class="form-group">
                                        <label>Dots Style</label>
                                        <select class="form-control" id="dotType">
                                            <option value="square">Square</option>
                                            <option value="dots">Dots</option>
                                            <option value="rounded">Rounded</option>
                                            <option value="classy">Classy</option>
                                            <option value="classy-rounded">Classy Rounded</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Color Type</label>
                                        <select class="form-control" id="dotColorType">
                                            <option value="single">Single Color</option>
                                            <option value="gradient">Color Gradient</option>
                                        </select>
                                    </div>
                                    
                                    <div id="dotSingleColor">
                                        <div class="form-group">
                                            <label>Dots Color</label>
                                            <input type="color" class="form-control" id="dotColor" value="#000000">
                                        </div>
                                    </div>
                                    
                                    <div id="dotGradient" style="display: none;">
                                        <div class="form-group">
                                            <label>Gradient Type</label>
                                            <select class="form-control" id="dotGradientType">
                                                <option value="linear">Linear</option>
                                                <option value="radial">Radial</option>
                                            </select>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Gradient Color 1</label>
                                                    <input type="color" class="form-control" id="dotGradientColor1" value="#000000">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Gradient Color 2</label>
                                                    <input type="color" class="form-control" id="dotGradientColor2" value="#595959">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Rotation (degrees)</label>
                                            <input type="range" class="form-control-range" id="dotGradientRotation" min="0" max="360" value="0">
                                            <div class="d-flex justify-content-between">
                                                <small>0°</small>
                                                <small id="dotGradientRotationValue">0°</small>
                                                <small>360°</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Corners Options -->
                                <div class="tab-pane fade" id="corners" role="tabpanel">
                                    <div class="form-group">
                                        <label>Corners Square Style</label>
                                        <select class="form-control" id="cornerType">
                                            <option value="square">Square</option>
                                            <option value="dot">Dot</option>
                                            <option value="extra-rounded">Extra Rounded</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Color Type</label>
                                        <select class="form-control" id="cornerColorType">
                                            <option value="single">Single Color</option>
                                            <option value="gradient">Color Gradient</option>
                                        </select>
                                    </div>
                                    
                                    <div id="cornerSingleColor">
                                        <div class="form-group">
                                            <label>Corners Color</label>
                                            <input type="color" class="form-control" id="cornerColor" value="#000000">
                                        </div>
                                    </div>
                                    
                                    <div id="cornerGradient" style="display: none;">
                                        <div class="form-group">
                                            <label>Gradient Type</label>
                                            <select class="form-control" id="cornerGradientType">
                                                <option value="linear">Linear</option>
                                                <option value="radial">Radial</option>
                                            </select>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Gradient Color 1</label>
                                                    <input type="color" class="form-control" id="cornerGradientColor1" value="#000000">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Gradient Color 2</label>
                                                    <input type="color" class="form-control" id="cornerGradientColor2" value="#595959">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Rotation (degrees)</label>
                                            <input type="range" class="form-control-range" id="cornerGradientRotation" min="0" max="360" value="0">
                                            <div class="d-flex justify-content-between">
                                                <small>0°</small>
                                                <small id="cornerGradientRotationValue">0°</small>
                                                <small>360°</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Corner Dots Options -->
                                <div class="tab-pane fade" id="corner-dots" role="tabpanel">
                                    <div class="form-group">
                                        <label>Corner Dots Style</label>
                                        <select class="form-control" id="cornerDotType">
                                            <option value="square">Square</option>
                                            <option value="dot">Dot</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Color Type</label>
                                        <select class="form-control" id="cornerDotColorType">
                                            <option value="single">Single Color</option>
                                            <option value="gradient">Color Gradient</option>
                                        </select>
                                    </div>
                                    
                                    <div id="cornerDotSingleColor">
                                        <div class="form-group">
                                            <label>Corner Dots Color</label>
                                            <input type="color" class="form-control" id="cornerDotColor" value="#000000">
                                        </div>
                                    </div>
                                    
                                    <div id="cornerDotGradient" style="display: none;">
                                        <div class="form-group">
                                            <label>Gradient Type</label>
                                            <select class="form-control" id="cornerDotGradientType">
                                                <option value="linear">Linear</option>
                                                <option value="radial">Radial</option>
                                            </select>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Gradient Color 1</label>
                                                    <input type="color" class="form-control" id="cornerDotGradientColor1" value="#000000">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Gradient Color 2</label>
                                                    <input type="color" class="form-control" id="cornerDotGradientColor2" value="#595959">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Rotation (degrees)</label>
                                            <input type="range" class="form-control-range" id="cornerDotGradientRotation" min="0" max="360" value="0">
                                            <div class="d-flex justify-content-between">
                                                <small>0°</small>
                                                <small id="cornerDotGradientRotationValue">0°</small>
                                                <small>360°</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Background Options -->
                                <div class="tab-pane fade" id="background" role="tabpanel">
                                    <div class="form-group">
                                        <label>Color Type</label>
                                        <select class="form-control" id="bgColorType">
                                            <option value="single">Single Color</option>
                                            <option value="gradient">Color Gradient</option>
                                        </select>
                                    </div>
                                    
                                    <div id="bgSingleColor">
                                        <div class="form-group">
                                            <label>Background Color</label>
                                            <input type="color" class="form-control" id="bgColor" value="#ffffff">
                                        </div>
                                    </div>
                                    
                                    <div id="bgGradient" style="display: none;">
                                        <div class="form-group">
                                            <label>Gradient Type</label>
                                            <select class="form-control" id="bgGradientType">
                                                <option value="linear">Linear</option>
                                                <option value="radial">Radial</option>
                                            </select>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Gradient Color 1</label>
                                                    <input type="color" class="form-control" id="bgGradientColor1" value="#ffffff">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Gradient Color 2</label>
                                                    <input type="color" class="form-control" id="bgGradientColor2" value="#f5f5f5">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Rotation (degrees)</label>
                                            <input type="range" class="form-control-range" id="bgGradientRotation" min="0" max="360" value="0">
                                            <div class="d-flex justify-content-between">
                                                <small>0°</small>
                                                <small id="bgGradientRotationValue">0°</small>
                                                <small>360°</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Logo Options -->
                                <div class="tab-pane fade" id="logo" role="tabpanel">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="hideBackgroundDots" checked>
                                            <label class="custom-control-label" for="hideBackgroundDots">Hide Background Dots</label>
                                        </div>
                                        <small class="form-text text-muted">Creates a clear area around the logo</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="logoSize">Logo Size (% of QR)</label>
                                        <input type="range" class="form-control-range" id="logoSize" min="0.1" max="0.5" step="0.05" value="0.4">
                                        <div class="d-flex justify-content-between">
                                            <small>Small (10%)</small>
                                            <small id="logoSizeValue">40%</small>
                                            <small>Large (50%)</small>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="logoMargin">Logo Margin (px)</label>
                                        <input type="range" class="form-control-range" id="logoMargin" min="0" max="20" value="10">
                                        <div class="d-flex justify-content-between">
                                            <small>0px</small>
                                            <small id="logoMarginValue">10px</small>
                                            <small>20px</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Advanced Options -->
                                <div class="tab-pane fade" id="advanced" role="tabpanel">
                                    <div class="form-group">
                                        <label>Type Number</label>
                                        <select class="form-control" id="typeNumber">
                                            <option value="0">Auto</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                        </select>
                                        <small class="form-text text-muted">Higher values support more data</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Mode</label>
                                        <select class="form-control" id="mode">
                                            <option value="Byte">Byte</option>
                                            <option value="Numeric">Numeric</option>
                                            <option value="Alphanumeric">Alphanumeric</option>
                                            <option value="Kanji">Kanji</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Error Correction Level</label>
                                        <select class="form-control" id="errorCorrectionLevel">
                                            <option value="H">H - High (30%)</option>
                                            <option value="Q">Q - Quartile (25%)</option>
                                            <option value="M">M - Medium (15%)</option>
                                            <option value="L">L - Low (7%)</option>
                                        </select>
                                        <small class="form-text text-muted">Higher levels allow more data to be corrupted</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <div class="row justify-content-center">
                                    <div class="col-12 text-center">
                                        <button type="button" id="saveTemplateBtnBottom" class="btn btn-primary mx-2" style="width: 150px;">
                                            <i class="fas fa-save mr-1"></i> Save Template
                                        </button>
                                        <button type="button" id="downloadBtn" class="btn btn-success mx-2" style="width: 150px;">
                                            <i class="fas fa-download mr-1"></i> Download QR
                                        </button>
                                        <button type="button" id="exportJSONBtn" class="btn btn-info mx-2" style="width: 150px;">
                                            <i class="fas fa-file-export mr-1"></i> Export Settings
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Template Preview</h4>
                        <div class="card-header-action">
                            <div class="dropdown">
                                <button class="btn btn-success dropdown-toggle" type="button" id="downloadOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Download
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" id="downloadPngBtn">PNG</a>
                                    <a class="dropdown-item" href="#" id="downloadSvgBtn">SVG</a>
                                    <a class="dropdown-item" href="#" id="downloadJpgBtn">JPG</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body text-center" id="preview-section">
                        <h4 class="mb-4">QR Design Preview</h4>
                        <div id="qrcode-preview" style="margin: 0 auto;"></div>
                        <div id="preview-error" class="alert alert-danger mt-3" style="display: none;"></div>
                        <button class="btn btn-primary mt-3" id="refresh-preview">
                            <i class="fas fa-sync-alt"></i> Refresh Preview
                        </button>
                        <a href="#" class="btn btn-info mt-3" id="view-server-preview">
                            <i class="fas fa-server"></i> View Server Preview
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<!-- Primary SweetAlert2 from node_modules -->
<script src="{{ asset('node_modules/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<!-- Fallback to CDN if node_modules isn't available -->
<script>
    if (typeof Swal === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
        console.log('Using SweetAlert2 CDN fallback');
    }
</script>
<script src="{{ asset('node_modules/qr-code-styling/lib/qr-code-styling.js') }}"></script>
<!-- Fallback to CDN if node_modules isn't available -->
<script>
    window.addEventListener('error', function(e) {
        if (e.target.src && e.target.src.includes('qr-code-styling.js')) {
            console.log('Loading QR code styling from CDN');
            const fallbackScript = document.createElement('script');
            fallbackScript.src = 'https://unpkg.com/qr-code-styling@1.9.2/lib/qr-code-styling.js';
            document.head.appendChild(fallbackScript);
        }
    }, true);
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize QR Code Styling object
        let qrCode;
        let currentQrSettings = {};
        let previewAttempts = 0;
        const MAX_PREVIEW_ATTEMPTS = 3;
        
        // Initialize logoUrl variable to store logo URL
        let logoUrl = "";
        
        // Check if we're editing a template with an existing logo
        @if(isset($selectedTemplate) && $selectedTemplate->logo_path)
            logoUrl = "{{ Storage::url(str_replace('public/', '', $selectedTemplate->logo_path)) }}";
            console.log('Loaded existing logo URL:', logoUrl);
        @endif

        // Function to initialize QR Code object
        function initQrCode(options = {}) {
            try {
                previewAttempts = 0;
                const defaultOptions = {
                    width: parseInt($('#qrWidth').val()) || 300,
                    height: parseInt($('#qrHeight').val()) || 300,
                    type: 'svg',
                    data: $('#qrData').val(),
                    margin: parseInt($('#qrMargin').val()) || 10,
                    qrOptions: {
                        typeNumber: 0,
                        mode: 'Byte',
                        errorCorrectionLevel: 'H'
                    },
                    dotsOptions: {
                        color: $('#dotColor').val(),
                        type: $('#dotType').val()
                    },
                    backgroundOptions: {
                        color: $('#bgColor').val()
                    }
                };
                
                // Add logo if available
                if (logoUrl) {
                    defaultOptions.image = logoUrl;
                    defaultOptions.imageOptions = {
                        hideBackgroundDots: $('#hideBackgroundDots').is(':checked'),
                        imageSize: parseFloat($('#logoSize').val()) || 0.4,
                        margin: parseInt($('#logoMargin').val()) || 10,
                        crossOrigin: "anonymous"
                    };
                    console.log('Including logo in QR code initialization:', logoUrl);
                }
                
                // Merge default options with provided options
                const mergedOptions = {...defaultOptions, ...options};
                
                // Validate dimensions to prevent browser freeze
                if (mergedOptions.width <= 0 || mergedOptions.width > 1000) {
                    console.warn('Invalid width detected, using default 300px');
                    mergedOptions.width = 300;
                    $('#qrWidth').val(300);
                }
                
                if (mergedOptions.height <= 0 || mergedOptions.height > 1000) {
                    console.warn('Invalid height detected, using default 300px');
                    mergedOptions.height = 300;
                    $('#qrHeight').val(300);
                }
                
                if (mergedOptions.margin < 0 || mergedOptions.margin > 50) {
                    console.warn('Invalid margin detected, using default 10px');
                    mergedOptions.margin = 10;
                    $('#qrMargin').val(10);
                }
                
                // Save current settings for later reference
                currentQrSettings = mergedOptions;
                
                // Create new QR code object
                qrCode = new QRCodeStyling(mergedOptions);
                
                // Clear previous content and append new QR code
                $('#qrcode-preview').empty();
                $('#preview-error').hide();
                
                qrCode.append(document.getElementById('qrcode-preview'));
                
                // Update the server preview URL
                updateServerPreviewUrl();
                
                return true;
            } catch (error) {
                console.error('Error initializing QR code:', error);
                $('#preview-error').text('Error creating QR code: ' + error.message).show();
                return false;
            }
        }
        
        // Function to update QR code with current settings
        function updateQrCode() {
            try {
                previewAttempts++;
                if (previewAttempts > MAX_PREVIEW_ATTEMPTS) {
                    $('#preview-error').text('Multiple errors occurred. Please check your settings and try again.').show();
                    return;
                }
                
                // Get all current settings
                const options = getQrOptions();
                
                // Validate the settings
                if (!validateQrSettings(options)) {
                return;
            }
            
                // If QR code object exists, update it
                if (qrCode) {
                    qrCode.update(options);
                    currentQrSettings = options;
                    $('#preview-error').hide();
                    updateServerPreviewUrl();
                    } else {
                    // Otherwise, initialize it
                    initQrCode(options);
                    }
                } catch (error) {
                console.error('Error updating QR code:', error);
                $('#preview-error').text('Error updating QR code: ' + error.message).show();
            }
        }
        
        // Validate QR settings to prevent errors
        function validateQrSettings(settings) {
            // Check required values
            if (!settings.data) {
                $('#preview-error').text('QR data cannot be empty').show();
                return false;
            }
            
            // Validate dimensions
            if (settings.width <= 0 || settings.height <= 0) {
                $('#preview-error').text('Width and height must be positive values').show();
                return false;
            }
            
            // Validate margin
            if (settings.margin < 0) {
                $('#preview-error').text('Margin cannot be negative').show();
                return false;
            }
            
            return true;
        }
        
        // Update server preview URL with template ID
        function updateServerPreviewUrl() {
            const templateId = $('#templateId').val();
            if (templateId) {
                const previewUrl = `/custom-qr/${templateId}/preview?data=${encodeURIComponent($('#qrData').val())}`;
                $('#view-server-preview').attr('href', previewUrl);
            }
        }
        
        // Event handler for the refresh preview button
        $('#refresh-preview').on('click', function(e) {
            e.preventDefault();
            previewAttempts = 0; // Reset attempt counter
            $('#preview-error').hide();
            updateQrCode();
        });

        // Save Template button in header
        document.getElementById('saveTemplateBtn').addEventListener('click', function() {
            saveQRTemplate();
        });
        
        // Save Template button at the bottom
        document.getElementById('saveTemplateBtnBottom').addEventListener('click', function() {
            saveQRTemplate();
        });
        
        // Download QR code
        document.getElementById('downloadBtn').addEventListener('click', function() {
            if (qrCode) {
                qrCode.download({ 
                    name: 'qrcode', 
                    extension: 'png' 
                });
            } else {
                alert('Please generate a QR code first');
            }
        });

        // Download specific format
        document.getElementById('downloadPngBtn').addEventListener('click', function() {
            if (qrCode) {
                qrCode.download({ 
                    name: 'qrcode', 
                    extension: 'png' 
                });
            } else {
                alert('Please generate a QR code first');
            }
        });

        document.getElementById('downloadSvgBtn').addEventListener('click', function() {
            if (qrCode) {
                qrCode.download({ 
                    name: 'qrcode', 
                    extension: 'svg' 
                });
            } else {
                alert('Please generate a QR code first');
            }
        });

        document.getElementById('downloadJpgBtn').addEventListener('click', function() {
            if (qrCode) {
                qrCode.download({ 
                    name: 'qrcode', 
                    extension: 'jpeg' 
                });
            } else {
                alert('Please generate a QR code first');
            }
        });
        
        // Export settings as JSON
        document.getElementById('exportJSONBtn').addEventListener('click', function() {
            const options = getQrOptions();
            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(options, null, 2));
            const downloadAnchorNode = document.createElement('a');
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", "qr-code-settings.json");
            document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
        });
        
                    // Handle logo file upload
        document.getElementById('logoUpload').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                // Read the file and convert to base64 to avoid using blob URLs
                const reader = new FileReader();
                reader.onload = function(event) {
                    logoUrl = event.target.result; // This will be a data URL (base64)
                    console.log('New logo uploaded and converted to base64:', logoUrl.substring(0, 30) + '...');
                    // Update QR code with new logo
                    updateQrCode();
                };
                reader.onerror = function() {
                    console.error('Error reading logo file');
                };
                reader.readAsDataURL(e.target.files[0]);
            } else {
                console.log('No new logo file selected, keeping existing logo:', logoUrl);
            }
            
            // Update file input label
            const fileName = e.target.value.split('\\').pop();
            const fileLabel = e.target.nextElementSibling;
            fileLabel.textContent = fileName || 'Choose logo';
        });
        
        // Add input change listeners for all form elements
        const formInputs = document.querySelectorAll('#qrGeneratorForm input, #qrGeneratorForm select');
        formInputs.forEach(input => {
            input.addEventListener('change', function() {
                updateQrCode();
            });
        });
        
        // Link width and height fields to maintain a square QR code
        document.getElementById('qrWidth').addEventListener('change', function() {
            // Update height to match width
            document.getElementById('qrHeight').value = this.value;
        });
        
        document.getElementById('qrHeight').addEventListener('change', function() {
            // Update width to match height
            document.getElementById('qrWidth').value = this.value;
        });
        
        // Initialize form with existing template settings if available
        @if(isset($selectedTemplate) && $selectedTemplate->settings_json)
            try {
                const templateSettings = {!! $selectedTemplate->settings_json !!};
                console.log('Loading template settings:', templateSettings);
                
                // Fill in form values from template settings
                if (templateSettings.width) $('#qrWidth').val(templateSettings.width);
                if (templateSettings.height) $('#qrHeight').val(templateSettings.height);
                if (templateSettings.margin) $('#qrMargin').val(templateSettings.margin);
                
                // Fill in advanced options
                if (templateSettings.qrOptions) {
                    if (templateSettings.qrOptions.typeNumber !== undefined) $('#typeNumber').val(templateSettings.qrOptions.typeNumber);
                    if (templateSettings.qrOptions.mode) $('#mode').val(templateSettings.qrOptions.mode);
                    if (templateSettings.qrOptions.errorCorrectionLevel) $('#errorCorrectionLevel').val(templateSettings.qrOptions.errorCorrectionLevel);
                }
                
                // Fill in dots options
                if (templateSettings.dotsOptions) {
                    if (templateSettings.dotsOptions.type) $('#dotType').val(templateSettings.dotsOptions.type);
                    
                    if (templateSettings.dotsOptions.gradient) {
                        $('#dotColorType').val('gradient');
                        if (templateSettings.dotsOptions.gradient.type) $('#dotGradientType').val(templateSettings.dotsOptions.gradient.type);
                        if (templateSettings.dotsOptions.gradient.rotation !== undefined) $('#dotGradientRotation').val(templateSettings.dotsOptions.gradient.rotation);
                        if (templateSettings.dotsOptions.gradient.colorStops && templateSettings.dotsOptions.gradient.colorStops.length >= 2) {
                            $('#dotGradientColor1').val(templateSettings.dotsOptions.gradient.colorStops[0].color);
                            $('#dotGradientColor2').val(templateSettings.dotsOptions.gradient.colorStops[1].color);
                        }
                    } else if (templateSettings.dotsOptions.color) {
                        $('#dotColorType').val('single');
                        $('#dotColor').val(templateSettings.dotsOptions.color);
                    }
                }
                
                // Fill in corners options
                if (templateSettings.cornersSquareOptions) {
                    if (templateSettings.cornersSquareOptions.type) $('#cornerType').val(templateSettings.cornersSquareOptions.type);
                    
                    if (templateSettings.cornersSquareOptions.gradient) {
                        $('#cornerColorType').val('gradient');
                        if (templateSettings.cornersSquareOptions.gradient.type) $('#cornerGradientType').val(templateSettings.cornersSquareOptions.gradient.type);
                        if (templateSettings.cornersSquareOptions.gradient.rotation !== undefined) $('#cornerGradientRotation').val(templateSettings.cornersSquareOptions.gradient.rotation);
                        if (templateSettings.cornersSquareOptions.gradient.colorStops && templateSettings.cornersSquareOptions.gradient.colorStops.length >= 2) {
                            $('#cornerGradientColor1').val(templateSettings.cornersSquareOptions.gradient.colorStops[0].color);
                            $('#cornerGradientColor2').val(templateSettings.cornersSquareOptions.gradient.colorStops[1].color);
                        }
                    } else if (templateSettings.cornersSquareOptions.color) {
                        $('#cornerColorType').val('single');
                        $('#cornerColor').val(templateSettings.cornersSquareOptions.color);
                    }
                }
                
                // Fill in corner dots options
                if (templateSettings.cornersDotOptions) {
                    if (templateSettings.cornersDotOptions.type) $('#cornerDotType').val(templateSettings.cornersDotOptions.type);
                    
                    if (templateSettings.cornersDotOptions.gradient) {
                        $('#cornerDotColorType').val('gradient');
                        if (templateSettings.cornersDotOptions.gradient.type) $('#cornerDotGradientType').val(templateSettings.cornersDotOptions.gradient.type);
                        if (templateSettings.cornersDotOptions.gradient.rotation !== undefined) $('#cornerDotGradientRotation').val(templateSettings.cornersDotOptions.gradient.rotation);
                        if (templateSettings.cornersDotOptions.gradient.colorStops && templateSettings.cornersDotOptions.gradient.colorStops.length >= 2) {
                            $('#cornerDotGradientColor1').val(templateSettings.cornersDotOptions.gradient.colorStops[0].color);
                            $('#cornerDotGradientColor2').val(templateSettings.cornersDotOptions.gradient.colorStops[1].color);
                        }
                    } else if (templateSettings.cornersDotOptions.color) {
                        $('#cornerDotColorType').val('single');
                        $('#cornerDotColor').val(templateSettings.cornersDotOptions.color);
                    }
                }
                
                // Fill in background options
                if (templateSettings.backgroundOptions) {
                    if (templateSettings.backgroundOptions.gradient) {
                        $('#bgColorType').val('gradient');
                        if (templateSettings.backgroundOptions.gradient.type) $('#bgGradientType').val(templateSettings.backgroundOptions.gradient.type);
                        if (templateSettings.backgroundOptions.gradient.rotation !== undefined) $('#bgGradientRotation').val(templateSettings.backgroundOptions.gradient.rotation);
                        if (templateSettings.backgroundOptions.gradient.colorStops && templateSettings.backgroundOptions.gradient.colorStops.length >= 2) {
                            $('#bgGradientColor1').val(templateSettings.backgroundOptions.gradient.colorStops[0].color);
                            $('#bgGradientColor2').val(templateSettings.backgroundOptions.gradient.colorStops[1].color);
                        }
                    } else if (templateSettings.backgroundOptions.color) {
                        $('#bgColorType').val('single');
                        $('#bgColor').val(templateSettings.backgroundOptions.color);
                    }
                }
                
                // Fill in logo options if available
                if (templateSettings.imageOptions) {
                    if (templateSettings.imageOptions.hideBackgroundDots !== undefined) $('#hideBackgroundDots').prop('checked', templateSettings.imageOptions.hideBackgroundDots);
                    if (templateSettings.imageOptions.imageSize !== undefined) $('#logoSize').val(templateSettings.imageOptions.imageSize);
                    if (templateSettings.imageOptions.margin !== undefined) $('#logoMargin').val(templateSettings.imageOptions.margin);
                }
                
                // Handle logo
                if (templateSettings.image) {
                    // If image URL is in the settings, use it
                    logoUrl = templateSettings.image;
                }
                
                // Toggle visibility of gradient options based on loaded settings
                toggleGradientOptions();
            } catch (error) {
                console.error('Error loading template settings:', error);
            }
        @endif
        
        // Initialize QR code with current settings
        initQrCode();
        
        // Toggle gradient options visibility based on select values
        function toggleGradientOptions() {
            // Dots gradient
            if ($('#dotColorType').val() === 'gradient') {
                $('#dotGradient').show();
                $('#dotSingleColor').hide();
            } else {
                $('#dotGradient').hide();
                $('#dotSingleColor').show();
            }
            
            // Corners gradient
            if ($('#cornerColorType').val() === 'gradient') {
                $('#cornerGradient').show();
                $('#cornerSingleColor').hide();
            } else {
                $('#cornerGradient').hide();
                $('#cornerSingleColor').show();
            }
            
            // Corner dots gradient
            if ($('#cornerDotColorType').val() === 'gradient') {
                $('#cornerDotGradient').show();
                $('#cornerDotSingleColor').hide();
            } else {
                $('#cornerDotGradient').hide();
                $('#cornerDotSingleColor').show();
            }
            
            // Background gradient
            if ($('#bgColorType').val() === 'gradient') {
                $('#bgGradient').show();
                $('#bgSingleColor').hide();
            } else {
                $('#bgGradient').hide();
                $('#bgSingleColor').show();
            }
        }
        
        // Add event listeners for color type changes to toggle gradients
        $('#dotColorType, #cornerColorType, #cornerDotColorType, #bgColorType').on('change', toggleGradientOptions);
        
        // Get all QR options from form fields
        function getQrOptions() {
            const data = document.getElementById('qrData').value || 'https://example.com';
            const width = parseInt(document.getElementById('qrWidth').value);
            const height = parseInt(document.getElementById('qrHeight').value);
            const margin = parseInt(document.getElementById('qrMargin').value);
            
            // Basic configuration
            const options = {
                width: width,
                height: height,
                type: "svg",
                data: data,
                margin: margin,
                qrOptions: {
                    typeNumber: parseInt(document.getElementById('typeNumber').value),
                    mode: document.getElementById('mode').value,
                    errorCorrectionLevel: document.getElementById('errorCorrectionLevel').value
                }
            };
            
            // Dots options
            if (document.getElementById('dotColorType').value === 'single') {
                options.dotsOptions = {
                    color: document.getElementById('dotColor').value,
                    type: document.getElementById('dotType').value
                };
            } else {
                options.dotsOptions = {
                    type: document.getElementById('dotType').value,
                    gradient: {
                        type: document.getElementById('dotGradientType').value,
                        rotation: parseInt(document.getElementById('dotGradientRotation').value),
                        colorStops: [
                            { offset: 0, color: document.getElementById('dotGradientColor1').value },
                            { offset: 1, color: document.getElementById('dotGradientColor2').value }
                        ]
                    }
                };
            }
            
            // Corners square options
            if (document.getElementById('cornerColorType').value === 'single') {
                options.cornersSquareOptions = {
                    color: document.getElementById('cornerColor').value,
                    type: document.getElementById('cornerType').value,
                };
            } else {
                options.cornersSquareOptions = {
                    type: document.getElementById('cornerType').value,
                    gradient: {
                        type: document.getElementById('cornerGradientType').value,
                        rotation: parseInt(document.getElementById('cornerGradientRotation').value),
                        colorStops: [
                            { offset: 0, color: document.getElementById('cornerGradientColor1').value },
                            { offset: 1, color: document.getElementById('cornerGradientColor2').value }
                        ]
                    }
                };
            }
            
            // Corners dot options
            if (document.getElementById('cornerDotColorType').value === 'single') {
                options.cornersDotOptions = {
                    color: document.getElementById('cornerDotColor').value,
                    type: document.getElementById('cornerDotType').value,
                };
            } else {
                options.cornersDotOptions = {
                    type: document.getElementById('cornerDotType').value,
                    gradient: {
                        type: document.getElementById('cornerDotGradientType').value,
                        rotation: parseInt(document.getElementById('cornerDotGradientRotation').value),
                        colorStops: [
                            { offset: 0, color: document.getElementById('cornerDotGradientColor1').value },
                            { offset: 1, color: document.getElementById('cornerDotGradientColor2').value }
                        ]
                    }
                };
            }
            
            // Background options
            if (document.getElementById('bgColorType').value === 'single') {
                options.backgroundOptions = {
                    color: document.getElementById('bgColor').value,
                };
            } else {
                options.backgroundOptions = {
                    gradient: {
                        type: document.getElementById('bgGradientType').value,
                        rotation: parseInt(document.getElementById('bgGradientRotation').value),
                        colorStops: [
                            { offset: 0, color: document.getElementById('bgGradientColor1').value },
                            { offset: 1, color: document.getElementById('bgGradientColor2').value }
                        ]
                    }
                };
            }
            
            // Add logo if available
            if (logoUrl) {
                // For server URLs and existing images, use the URL directly
                if (logoUrl.includes('/storage/') || logoUrl.includes('/img/')) {
                    console.log('Using server path for logo:', logoUrl);
                    options.image = logoUrl;
                    options.imageOptions = {
                        hideBackgroundDots: document.getElementById('hideBackgroundDots').checked,
                        imageSize: parseFloat(document.getElementById('logoSize').value),
                        margin: parseInt(document.getElementById('logoMargin').value),
                        crossOrigin: "anonymous",
                    };
                }
                // Handle blob URLs for newly uploaded files
                else if (logoUrl.startsWith('blob:')) {
                    console.log('Using blob URL for logo, will be converted to base64 by server:', logoUrl);
                    options.image = logoUrl;
                        options.imageOptions = {
                            hideBackgroundDots: document.getElementById('hideBackgroundDots').checked,
                            imageSize: parseFloat(document.getElementById('logoSize').value),
                            margin: parseInt(document.getElementById('logoMargin').value),
                            crossOrigin: "anonymous",
                        };
                }
                // For anything else (like base64 data URLs)
                else {
                    console.log('Using other logo URL format:', logoUrl.substring(0, 30) + '...');
                options.image = logoUrl;
                options.imageOptions = {
                    hideBackgroundDots: document.getElementById('hideBackgroundDots').checked,
                    imageSize: parseFloat(document.getElementById('logoSize').value),
                    margin: parseInt(document.getElementById('logoMargin').value),
                    crossOrigin: "anonymous",
                };
                }
            }
            
            // Log full options for debugging
            console.log('QR Options being generated:', JSON.stringify(options));
            
            return options;
        }
        
        // Function to save QR template
        function saveQRTemplate() {
            console.log('Saving QR template...');
            
            // Check if SweetAlert is available
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 is not loaded');
                alert('Save functionality requires SweetAlert2, which seems to be missing. Using basic prompt instead.');
                
                // Basic fallback using prompt dialog
                const templateName = prompt('Enter template name:', 
                    @if(isset($selectedTemplate)) "{{ $selectedTemplate->name }}" @else "Template " + new Date().toLocaleString() @endif
                );
                
                if (!templateName) {
                    return; // User cancelled
                }
                
                saveTemplateToServer(templateName);
                return;
            }
            
            // Using SweetAlert for name input
            Swal.fire({
                title: 'Save Template',
                input: 'text',
                inputLabel: 'Template Name',
                inputValue: @if(isset($selectedTemplate)) "{{ $selectedTemplate->name }}" @else "Template " + new Date().toLocaleString() @endif,
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to provide a template name!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    saveTemplateToServer(result.value);
                }
            });
        }
        
        // Function to save template to server
        function saveTemplateToServer(templateName) {
            // Show loading indicator
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait while we save your template',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            } else {
                alert('Saving template...');
            }
            
            // Get current QR settings
            const options = getQrOptions();
            
            // Create a sample image of the QR code
            let sampleImage = '';
            try {
                // Try to capture the current QR code preview
                const qrElement = document.querySelector('#qrcode-preview svg, #qrcode-preview canvas');
                if (qrElement) {
                    const canvas = document.createElement('canvas');
                    const rect = qrElement.getBoundingClientRect();
                    canvas.width = rect.width;
                    canvas.height = rect.height;
                    
                    const ctx = canvas.getContext('2d');
                    
                    // For SVG elements, we need to convert to an image first
                    if (qrElement.tagName.toLowerCase() === 'svg') {
                        const svgData = new XMLSerializer().serializeToString(qrElement);
                        const svgBlob = new Blob([svgData], {type: 'image/svg+xml;charset=utf-8'});
                        const url = URL.createObjectURL(svgBlob);
                        
                        const img = new Image();
                        img.onload = function() {
                            ctx.drawImage(img, 0, 0, rect.width, rect.height);
                            URL.revokeObjectURL(url);
                            
                            // Get the base64 data and save it
                            sampleImage = canvas.toDataURL('image/png');
                            sendSaveRequest(templateName, options, sampleImage);
                        };
                        
                        img.onerror = function() {
                            console.error('Error loading SVG image');
                            sendSaveRequest(templateName, options, null);
                        };
                        
                        img.src = url;
                        return; // Return here as we're handling the save in the onload callback
                    } else {
                        // For canvas elements, we can draw directly
                        ctx.drawImage(qrElement, 0, 0, rect.width, rect.height);
                        sampleImage = canvas.toDataURL('image/png');
                    }
                }
            } catch (error) {
                console.error('Error capturing QR preview:', error);
            }
            
            // Send save request (for non-SVG cases)
            sendSaveRequest(templateName, options, sampleImage);
        }
        
        // Function to send the actual save request to the server
        function sendSaveRequest(templateName, options, sampleImage) {
            // Prepare data to send
            const data = {
                name: templateName,
                qr_settings: JSON.stringify(options),
                template_id: document.getElementById('templateId').value || null,
            };
            
            // Add sample image if available
            if (sampleImage) {
                data.sample_image = sampleImage;
            }
            
            console.log('Sending template data to server:', { 
                name: data.name, 
                template_id: data.template_id
            });
            
            // Get the CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            if (!csrfToken) {
                console.error('CSRF token not found');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'CSRF token not found. Please refresh the page and try again.'
                    });
                } else {
                    alert('Error: CSRF token not found. Please refresh the page and try again.');
                }
                return;
            }
            
            // Send to server
            fetch('{{ route("custom-qr.saveTemplate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    console.error('Server response not OK:', response.status, response.statusText);
                    
                    // Try to get response text for more details
                    return response.text().then(text => {
                        throw new Error(`Failed to save template. Status: ${response.status}. ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Template Saved!',
                            text: data.message || 'Your template has been saved successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Template saved successfully!');
                    }
                    
                    // Redirect to list after successful save
                    setTimeout(() => {
                        window.location.href = '{{ route("custom-qr.index") }}';
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to save template');
                }
            })
            .catch(error => {
                console.error('Error saving template:', error);
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message || 'An error occurred while saving the template.'
                    });
                } else {
                    alert('Error: ' + (error.message || 'Failed to save template'));
                }
            });
        }
    });
</script>
@endpush
@endsection 