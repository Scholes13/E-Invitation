@extends('template.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>RSVP Settings</h1>
        </div>

        <div class="section-body">
            <h2 class="section-title">RSVP Configuration</h2>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Update RSVP Settings</h4>
                        </div>
                        <div class="card-body">
                            <form id="rsvpSettingsForm" action="{{ route('setting.rsvpSettingsUpdate') }}" method="POST">
                                @csrf
                                @method('PUT')

                                @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                                @endif

                                <div class="form-group">
                                    <div class="control-label">Enable RSVP Feature</div>
                                    <div class="mt-2 mb-2">
                                        <label class="mt-2">
                                            <input type="radio" name="enable_rsvp" value="1" {{ $setting->enable_rsvp ? 'checked' : '' }}>
                                            <span class="ml-2">Enable</span>
                                        </label>
                                        <label class="ml-4">
                                            <input type="radio" name="enable_rsvp" value="0" {{ !$setting->enable_rsvp ? 'checked' : '' }}>
                                            <span class="ml-2">Disable</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">When enabled, guests can respond to invitations</small>
                                </div>

                                <div class="form-group">
                                    <label for="rsvp_deadline">RSVP Deadline</label>
                                    <input type="date" class="form-control" id="rsvp_deadline" name="rsvp_deadline" value="{{ $setting->rsvp_deadline ?? '' }}">
                                    <small class="form-text text-muted">The deadline for guests to respond</small>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Allow Plus Ones</div>
                                    <div class="mt-2 mb-2">
                                        <label class="mt-2">
                                            <input type="radio" name="enable_plus_ones" value="1" {{ $setting->enable_plus_ones ? 'checked' : '' }}>
                                            <span class="ml-2">Enable</span>
                                        </label>
                                        <label class="ml-4">
                                            <input type="radio" name="enable_plus_ones" value="0" {{ !$setting->enable_plus_ones ? 'checked' : '' }}>
                                            <span class="ml-2">Disable</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Allow guests to bring additional companions</small>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Collect Dietary Preferences</div>
                                    <div class="mt-2 mb-2">
                                        <label class="mt-2">
                                            <input type="radio" name="collect_dietary_preferences" value="1" {{ $setting->collect_dietary_preferences ? 'checked' : '' }}>
                                            <span class="ml-2">Enable</span>
                                        </label>
                                        <label class="ml-4">
                                            <input type="radio" name="collect_dietary_preferences" value="0" {{ !$setting->collect_dietary_preferences ? 'checked' : '' }}>
                                            <span class="ml-2">Disable</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Ask guests for dietary preferences/restrictions</small>
                                </div>

                                <div class="form-group">
                                    <div class="control-label">Send RSVP Reminders</div>
                                    <div class="mt-2 mb-2">
                                        <label class="mt-2">
                                            <input type="radio" name="send_rsvp_reminders" value="1" {{ $setting->send_rsvp_reminders ? 'checked' : '' }}>
                                            <span class="ml-2">Enable</span>
                                        </label>
                                        <label class="ml-4">
                                            <input type="radio" name="send_rsvp_reminders" value="0" {{ !$setting->send_rsvp_reminders ? 'checked' : '' }}>
                                            <span class="ml-2">Disable</span>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Automatically send reminders to guests who haven't responded</small>
                                </div>

                                <div class="form-group">
                                    <label for="reminder_days_before_deadline">Days Before Deadline to Send Reminder</label>
                                    <input type="number" class="form-control" id="reminder_days_before_deadline" name="reminder_days_before_deadline" value="{{ $setting->reminder_days_before_deadline ?? 3 }}" min="1" max="30">
                                    <small class="form-text text-muted">How many days before the deadline to send reminder emails</small>
                                </div>

                                <div class="form-group">
                                    <label for="rsvp_email_subject">RSVP Reminder Email Subject</label>
                                    <input type="text" class="form-control" id="rsvp_email_subject" name="rsvp_email_subject" value="{{ $setting->rsvp_email_subject ?? 'Reminder: Please RSVP for our event' }}">
                                </div>

                                <div class="form-group">
                                    <label for="rsvp_email_template">RSVP Reminder Email Template</label>
                                    <textarea class="form-control summernote" id="rsvp_email_template" name="rsvp_email_template" rows="6">{{ $setting->rsvp_email_template ?? '<p>Dear {name},</p><p>This is a friendly reminder to RSVP for our upcoming event. We would appreciate your response by {deadline}.</p><p>To respond, please click the link below:</p><p><a href="{rsvp_link}">Click here to RSVP</a></p><p>Thank you!</p>' }}</textarea>
                                    <small class="form-text text-muted">
                                        Available placeholders: {name}, {deadline}, {rsvp_link}, {event_name}, {event_date}, {event_location}
                                    </small>
                                </div>

                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary" id="saveRsvpSettings">Save RSVP Settings</button>
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
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        // Add form submission handler
        $('#rsvpSettingsForm').on('submit', function() {
            console.log('Form is being submitted with values:');
            const formData = $(this).serializeArray();
            console.log(formData);
            return true; // Allow form submission to continue
        });
    });
</script>
@endpush 