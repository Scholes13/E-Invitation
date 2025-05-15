@extends('template.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Email Template Editor</h1>
        </div>

        <div class="section-body">
            <h2 class="section-title">Email Template Settings</h2>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Update Email Template</h4>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="alert alert-info">
                                <h5>Available Variables:</h5>
                                <ul>
                                    <li><code>{name}</code> - Guest Name</li>
                                    <li><code>{qrcode}</code> - QR Code Invitation</li>
                                    <li><code>{company}</code> - Guest Company</li>
                                    <li><code>{table}</code> - Table Number</li>
                                    <li><code>{type}</code> - Invitation Type</li>
                                </ul>
                                <p class="mb-0">Insert these variables into your email template where needed.</p>
                            </div>

                            <form action="{{ route('setting.emailTemplateUpdate') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="email_subject">Email Subject Template</label>
                                    <input type="text" id="email_subject" name="email_subject_template" 
                                          class="form-control" 
                                          value="@if(old('email_subject_template')){{ old('email_subject_template') }}@elseif(isset($setting->email_subject_template)){{ $setting->email_subject_template }}@else{!! "UNDANGAN untuk {name}" !!}@endif">
                                    <small class="text-muted">You can use variables like {name} in the subject</small>
                                </div>

                                <div class="form-group">
                                    <label>Email Template Editor</label>
                                    
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{name}')">Insert Name</button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{qrcode}')">Insert QR Code</button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{company}')">Insert Company</button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{table}')">Insert Table</button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{type}')">Insert Type</button>
                                    </div>
                                    
                                    <textarea id="email_editor" name="email_template_blasting" class="form-control">@if(old('email_template_blasting')){{ old('email_template_blasting') }}@elseif(isset($setting->email_template_blasting)){{ $setting->email_template_blasting }}@else<p>Dear {name},</p>

<p>We are delighted to invite you to our event.</p>

<p>Your personal invitation code is: {qrcode}</p>

<p>Thank You</p>@endif</textarea>
                                </div>

                                <div class="form-group">
                                    <label>Preview</label>
                                    <div id="emailPreview" class="border p-3" style="min-height: 200px; background-color: white;"></div>
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('styles')
<style>
    .tox-tinymce {
        min-height: 400px !important;
    }
    #email_editor {
        min-height: 400px;
    }
    .note-editor {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .note-toolbar {
        background-color: #f8f9fa;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#email_editor').summernote({
            placeholder: 'Write your email template here...',
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'italic', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function(contents) {
                    updatePreview(contents);
                }
            }
        });
        
        // Initial preview
        setTimeout(function() {
            updatePreview($('#email_editor').summernote('code'));
        }, 500);
    });
    
    function updatePreview(contents) {
        $('#emailPreview').html(contents);
    }
    
    function insertVariable(variable) {
        $('#email_editor').summernote('insertText', variable);
    }
</script>
@endpush

@endsection