@extends('template.template')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Advanced QR Code Styling</h1>
        </div>
        
        <div class="row">
            <div class="col-12 mb-4">
                <div class="hero bg-primary text-white">
                    <div class="hero-inner">
                        <h2>Create Beautiful QR Codes</h2>
                        <p class="lead">Generate custom QR codes with perfect logo placement and extensive styling options.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h4>QR Code Options</h4>
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
                                <button type="button" id="generateBtn" class="btn btn-primary">Generate QR Code</button>
                                <button type="button" id="downloadBtn" class="btn btn-success">Download QR</button>
                                <button type="button" id="exportJSONBtn" class="btn btn-info">Export Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h4>QR Preview</h4>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 400px;">
                        <div id="qrCodeContainer" class="text-center">
                            <div id="qrCode"></div>
                            <p class="text-muted mt-3">Your QR code will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="{{ asset('node_modules/qr-code-styling/lib/qr-code-styling.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initial QR code
        let qrCode;
        let logoUrl = "";
        
        // Toggle gradient options visibility
        function toggleGradientOptions() {
            // Dots gradient
            if (document.getElementById('dotColorType').value === 'gradient') {
                document.getElementById('dotGradient').style.display = 'block';
                document.getElementById('dotSingleColor').style.display = 'none';
            } else {
                document.getElementById('dotGradient').style.display = 'none';
                document.getElementById('dotSingleColor').style.display = 'block';
            }
            
            // Corners gradient
            if (document.getElementById('cornerColorType').value === 'gradient') {
                document.getElementById('cornerGradient').style.display = 'block';
                document.getElementById('cornerSingleColor').style.display = 'none';
            } else {
                document.getElementById('cornerGradient').style.display = 'none';
                document.getElementById('cornerSingleColor').style.display = 'block';
            }
            
            // Corner dots gradient
            if (document.getElementById('cornerDotColorType').value === 'gradient') {
                document.getElementById('cornerDotGradient').style.display = 'block';
                document.getElementById('cornerDotSingleColor').style.display = 'none';
            } else {
                document.getElementById('cornerDotGradient').style.display = 'none';
                document.getElementById('cornerDotSingleColor').style.display = 'block';
            }
            
            // Background gradient
            if (document.getElementById('bgColorType').value === 'gradient') {
                document.getElementById('bgGradient').style.display = 'block';
                document.getElementById('bgSingleColor').style.display = 'none';
            } else {
                document.getElementById('bgGradient').style.display = 'none';
                document.getElementById('bgSingleColor').style.display = 'block';
            }
        }
        
        // Add event listeners for color type changes
        document.getElementById('dotColorType').addEventListener('change', toggleGradientOptions);
        document.getElementById('cornerColorType').addEventListener('change', toggleGradientOptions);
        document.getElementById('cornerDotColorType').addEventListener('change', toggleGradientOptions);
        document.getElementById('bgColorType').addEventListener('change', toggleGradientOptions);
        
        // Show filename when file is selected
        document.getElementById('logoUpload').addEventListener('change', function(e) {
            let fileName = e.target.value.split('\\').pop();
            document.querySelector('.custom-file-label').innerHTML = fileName || "Choose logo";
            
            // Create URL for uploaded logo
            if (e.target.files && e.target.files[0]) {
                logoUrl = URL.createObjectURL(e.target.files[0]);
                // Generate updated QR
                generateQR();
            }
        });
        
        // Update range input value displays
        function setupRangeInput(id, valueId, suffix = '') {
            const input = document.getElementById(id);
            const value = document.getElementById(valueId);
            
            input.addEventListener('input', function() {
                value.textContent = input.value + suffix;
                generateQR(); // Update QR code when slider changes
            });
            
            // Set initial value
            value.textContent = input.value + suffix;
        }
        
        setupRangeInput('logoSize', 'logoSizeValue', '%');
        setupRangeInput('logoMargin', 'logoMarginValue', 'px');
        setupRangeInput('dotGradientRotation', 'dotGradientRotationValue', '°');
        setupRangeInput('cornerGradientRotation', 'cornerGradientRotationValue', '°');
        setupRangeInput('cornerDotGradientRotation', 'cornerDotGradientRotationValue', '°');
        setupRangeInput('bgGradientRotation', 'bgGradientRotationValue', '°');
        
        // Generate QR code button
        document.getElementById('generateBtn').addEventListener('click', function() {
            generateQR();
        });
        
        // Download QR code
        document.getElementById('downloadBtn').addEventListener('click', function() {
            if (qrCode) {
                qrCode.download({ 
                    name: 'styled-qr-code', 
                    extension: 'png' 
                });
            } else {
                alert('Please generate a QR code first');
            }
        });
        
        // Export settings as JSON
        document.getElementById('exportJSONBtn').addEventListener('click', function() {
            const options = getQROptions();
            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(options, null, 2));
            const downloadAnchorNode = document.createElement('a');
            downloadAnchorNode.setAttribute("href", dataStr);
            downloadAnchorNode.setAttribute("download", "qr-code-settings.json");
            document.body.appendChild(downloadAnchorNode);
            downloadAnchorNode.click();
            downloadAnchorNode.remove();
        });
        
        // Add input change listeners for all form elements
        const formInputs = document.querySelectorAll('#qrGeneratorForm input, #qrGeneratorForm select');
        formInputs.forEach(input => {
            input.addEventListener('change', function() {
                generateQR();
            });
        });
        
        // Get all QR options from form fields
        function getQROptions() {
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
                options.image = logoUrl;
                options.imageOptions = {
                    hideBackgroundDots: document.getElementById('hideBackgroundDots').checked,
                    imageSize: parseFloat(document.getElementById('logoSize').value),
                    margin: parseInt(document.getElementById('logoMargin').value),
                    crossOrigin: "anonymous",
                };
            }
            
            return options;
        }
        
        // Generate QR code function
        function generateQR() {
            // Clear previous QR code
            const container = document.getElementById('qrCode');
            container.innerHTML = '';
            
            // Get all options
            const options = getQROptions();
            
            // Create QR code
            qrCode = new QRCodeStyling(options);
            qrCode.append(container);
        }
        
        // Initialize display of gradient options
        toggleGradientOptions();
        
        // Generate initial QR code
        generateQR();
    });
</script>
@endpush
@endsection 