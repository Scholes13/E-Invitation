@extends('template.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Email Template Editor</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Email Template</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Email Subject</label>
                                    <input type="text" name="email_subject_template" 
                                           class="form-control"
                                           value="{{ $settings['email_subject_template'] ?? '' }}">
                                </div>

                                <div class="form-group">
                                    <label>Email Body Template</label>
                                    <textarea name="email_body_template" 
                                              class="form-control"
                                              style="height: 60vh; font-family: monospace"
                                              rows="25"
                                              placeholder="Gunakan HTML untuk formatting (contoh: <p>...</p>)">{{ old('email_body_template', $settings['email_body_template'] ?? '') }}</textarea>
                                    <small class="form-text text-muted">
                                        Gunakan variabel: {{ '$invitation->name_guest' }} dan {{ '$invitation->qrcode_invitation' }}
                                    </small>
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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