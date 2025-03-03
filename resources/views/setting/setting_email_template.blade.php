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
                                <h6>Available Variables:</h6>
                                <ul class="mb-0">
                                    <li><code>@{{ $guest->name_guest }}</code> - Guest Name</li>
                                    <li><code>@{{ $guest->code_guest }}</code> - Guest Code</li>
                                    <li><code>@{{ $guest->qr_code }}</code> - QR Code</li>
                                    <li><code>@{{ $invitation->table_number_invitation }}</code> - Table Number</li>
                                    <li><code>@{{ $invitation->type_invitation }}</code> - Invitation Type</li>
                                    <li><code>@{{ $invitation->information_invitation }}</code> - Additional Information</li>
                                    <li><code>@{{ $invitation->link_invitation }}</code> - Invitation Link</li>
                                </ul>
                            </div>

                            <form action="{{ route('setting.emailTemplateUpdate') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label for="email_subject">Email Subject Template</label>
                                    <div class="alert alert-info">
                                        Available Variables:
                                            `@{{ $guest->name_guest }}`,
                                            `@{{ $guest->code_guest }}`
                                    </div>
                                    <input type="text" id="email_subject" name="email_subject_template" class="form-control" value="@if(old('email_subject_template')){{ old('email_subject_template') }}@elseif(isset($setting->email_subject_template)){{ $setting->email_subject_template }}@else{!! "UNDANGAN" !!}@endif">
                                </div>

                                <div class="form-group">
                                    <label for="editor">Email Template Content</label>
                                    <textarea id="editor" name="email_template_blasting" class="form-control">@if(old('email_template_blasting')){{ old('email_template_blasting') }}@elseif(isset($setting->email_template_blasting)){{ $setting->email_template_blasting }}@else
<p>Dear @{{ $guest->name_guest }},</p>

<p>We are delighted to invite you to our wedding celebration.</p>

<p>Your personal invitation code is: @{{ $guest->code_guest }}</p>

<p>Thank You</p>
@endif</textarea>
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

@push('scripts')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', {
        height: 400,
        removeButtons: 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,About',
        toolbarGroups: [
            { name: 'document', groups: ['mode', 'document', 'doctools'] },
            { name: 'clipboard', groups: ['clipboard', 'undo'] },
            { name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing'] },
            { name: 'forms', groups: ['forms'] },
            '/',
            { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
            { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph'] },
            { name: 'links', groups: ['links'] },
            { name: 'insert', groups: ['insert'] },
            '/',
            { name: 'styles', groups: ['styles'] },
            { name: 'colors', groups: ['colors'] },
            { name: 'tools', groups: ['tools'] },
            { name: 'others', groups: ['others'] }
        ]
    });
</script>
@endpush