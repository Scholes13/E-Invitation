@extends('template.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Blasting</h1>
        </div>

        <div class="section-body">
            <h2 class="section-title">Email Blasting</h2>
            <p class="section-lead">
                Send invitation emails to registered guests.
            </p>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Send Invitations</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('blasting.send') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <button type="submit" name="send_type" value="unsent" class="btn btn-primary">
                                        Send to All Unsent Invitations
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Email Status</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Guest Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Sent At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invitations as $invitation)
                                        <tr>
                                            <td>{{ $invitation->guest->name_guest }}</td>
                                            <td>{{ $invitation->guest->email_guest }}</td>
                                            <td>
                                                @if($invitation->email_sent)
                                                    <span class="badge badge-success">Sent</span>
                                                    @if($invitation->email_read)
                                                        <span class="badge badge-info">Read</span>
                                                    @endif
                                                    @if($invitation->email_bounced)
                                                        <span class="badge badge-danger">Bounced</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-warning">Not Sent</span>
                                                @endif
                                            </td>
                                            <td>{{ $invitation->email_sent ? $invitation->updated_at->format('Y-m-d H:i:s') : '-' }}</td>
                                            <td>
                                                <form action="{{ route('blasting.send') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="id_invitation" value="{{ $invitation->id_invitation }}">
                                                    <button type="submit" class="btn btn-sm btn-primary" {{ !$invitation->guest->email_guest ? 'disabled' : '' }}>
                                                        {{ $invitation->email_sent ? 'Resend' : 'Send' }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection