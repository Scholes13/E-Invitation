@extends('template.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>App Settings</h1>
        </div>

        <div class="section-body">
            <h2 class="section-title">General Settings</h2>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Update Settings</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('setting.settingAppUpdate') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="name_app">App Name</label>
                                    <input type="text" class="form-control" id="name_app" name="name_app" value="{{ $setting->name_app ?? '' }}">
                                </div>
                                <div class="form-group">
                                    <label>App Logo</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="logo_app" name="logo_app">
                                        <label class="custom-file-label" for="logo_app">Choose file</label>
                                    </div>
                                    @if($setting->logo_app)
                                    <div class="mt-2">
                                        <img src="{{ asset('img/app/' . $setting->logo_app) }}" alt="App Logo" width="100">
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Background Color</label>
                                    <input type="color" class="form-control" id="color_bg_app" name="color_bg_app" value="{{ $setting->color_bg_app ?? '#ffffff' }}">
                                </div>
                                <div class="form-group">
                                    <label>Background Image</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="image_bg_app" name="image_bg_app">
                                        <label class="custom-file-label" for="image_bg_app">Choose file</label>
                                    </div>
                                    @if($setting->image_bg_app)
                                    <div class="mt-2">
                                        <img src="{{ asset('img/app/' . $setting->image_bg_app) }}" alt="Background Image" width="100">
                                    </div>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <div class="control-label">Background Image Status</div>
                                    <label class="custom-switch mt-2">
                                        <input type="checkbox" name="image_bg_status" class="custom-switch-input" {{ $setting->image_bg_status ? 'checked' : '' }}>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Show / Hide Background Image</span>
                                    </label>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email_template_blasting_preview">Email Template Blasting Preview</label>
                                    <div style="width: 100%;">
                                        <div style="border: 1px solid #ced4da; padding: 10px; border-radius: 5px; white-space: pre-line; width:100%; overflow-x: auto;">
                                            {{ Str::limit($setting->email_template_blasting ?? '', 400) }}
                                        </div>
                                        <a href="{{ route('setting.emailTemplate') }}" class="btn btn-sm btn-primary mt-2">Edit Template</a>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>RSVP Settings</label>
                                    <div class="d-flex">
                                        <div class="custom-control custom-switch mr-3">
                                            <input type="checkbox" class="custom-control-input" name="enable_rsvp" id="enable_rsvp" {{ $setting->enable_rsvp ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="enable_rsvp">Enable RSVP Feature</label>
                                        </div>
                                        <a href="{{ route('setting.rsvpSettings') }}" class="btn btn-sm btn-primary">Manage RSVP Settings</a>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Custom QR Design Settings</label>
                                    <div class="d-flex">
                                        <div class="custom-control custom-switch mr-3">
                                            <input type="checkbox" class="custom-control-input" name="enable_custom_qr" id="enable_custom_qr" {{ isset($setting->enable_custom_qr) && $setting->enable_custom_qr ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="enable_custom_qr">Enable Custom QR Design Feature</label>
                                        </div>
                                        <a href="{{ url('custom-qr') }}" class="btn btn-sm btn-primary">Manage Custom QR Templates</a>
                                    </div>
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection