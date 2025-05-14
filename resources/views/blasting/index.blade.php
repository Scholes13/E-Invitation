@extends('template.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Email Blasting</h1>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Send Invitations</h4>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            
                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <div class="row feature-cards">
                                <div class="col-md-6">
                                    <div class="card shadow-sm feature-card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">Send to All Unsent Invitations</h5>
                                            <p class="card-text flex-fill">This will send emails to all guests who haven't received an invitation yet.</p>
                                            <div class="card-action mt-auto">
                                                <form action="{{ route('blasting.send') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" name="send_type" value="unsent" class="btn btn-primary">
                                                        <i class="fas fa-paper-plane"></i> Send to All Unsent
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card shadow-sm feature-card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">Email Template Settings</h5>
                                            <p class="card-text flex-fill">Customize your email template with our visual editor.</p>
                                            <div class="card-action mt-auto">
                                                <a href="{{ route('setting.emailTemplate') }}" class="btn btn-info">
                                                    <i class="fas fa-edit"></i> Edit Email Template
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                <table class="table table-striped" id="invitation-table">
                                    <thead>
                                        <tr>
                                            <th>Guest Name</th>
                                            <th>Email</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invitations as $invitation)
                                        <tr>
                                            <td>{{ $invitation->name_guest }}</td>
                                            <td>{{ $invitation->email_guest }}</td>
                                            <td class="text-center">
                                                @if($invitation->email_bounced)
                                                    <span class="badge badge-danger">Bounced</span>
                                                @elseif($invitation->email_read)
                                                    <span class="badge badge-info">Read</span>
                                                @elseif($invitation->email_sent)
                                                    <span class="badge badge-success">Sent</span>
                                                @else
                                                    <span class="badge badge-warning">Not Sent</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('blasting.send') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="id_invitation" value="{{ $invitation->id_invitation }}">
                                                    <button type="submit" class="btn btn-sm btn-primary" {{ !$invitation->email_guest ? 'disabled' : '' }}>
                                                        <i class="fas fa-paper-plane"></i> {{ $invitation->email_sent ? 'Resend' : 'Send' }}
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

@push('scripts')
<script>
    $(document).ready(function() {
        $('#invitation-table').DataTable({
            "pageLength": 25,
            "order": [[ 2, "desc" ]]
        });
    });
</script>
@endpush

@push('styles')
<style>
    .badge {
        padding: 0.4em 0.6em;
        font-size: 90%;
        font-weight: 600;
    }
    .badge-success {
        background-color: #28a745;
    }
    .badge-info {
        background-color: #17a2b8;
    }
    .badge-warning {
        color: #212529;
        background-color: #ffc107;
    }
    .badge-danger {
        background-color: #dc3545;
    }
    
    /* Feature card styles to make boxes equal height */
    .feature-cards {
        display: flex;
        flex-wrap: wrap;
    }
    
    .feature-card {
        transition: all 0.3s ease;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .feature-card .card-body {
        padding: 25px;
        height: 100%;
    }
    
    .feature-card .card-title {
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .feature-card .card-text {
        margin-bottom: 20px;
    }
    
    .feature-card .card-action {
        display: flex;
        justify-content: flex-start;
    }
    
    .feature-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    @media (max-width: 767.98px) {
        .feature-card {
            margin-bottom: 15px;
        }
    }
</style>
@endpush