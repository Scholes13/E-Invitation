@extends('template.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>RSVP Management</h1>
        </div>

        <div class="section-body">
            <h2 class="section-title">RSVP Responses</h2>
            <p class="section-lead">Track and manage guest RSVP responses.</p>

            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Invitations</h4>
                            </div>
                            <div class="card-body">
                                {{ $stats['total'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Attending</h4>
                            </div>
                            <div class="card-body">
                                {{ $stats['yes'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="fas fa-times"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Not Attending</h4>
                            </div>
                            <div class="card-body">
                                {{ $stats['no'] }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="fas fa-question"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Pending</h4>
                            </div>
                            <div class="card-body">
                                {{ $stats['pending'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>RSVP Responses</h4>
                            <div class="card-header-action">
                                <a href="{{ route('setting.rsvpSettings') }}" class="btn btn-primary">
                                    <i class="fas fa-cog"></i> RSVP Settings
                                </a>
                                @if($setting->send_rsvp_reminders)
                                <form action="{{ route('rsvp.sendReminders') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-bell"></i> Send Reminders
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="rsvp-table">
                                    <thead>
                                        <tr>
                                            <th>Guest Name</th>
                                            <th>Contact</th>
                                            <th>Status</th>
                                            <th>Guests</th>
                                            <th>Dietary</th>
                                            <th>Response Date</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invitations as $invitation)
                                        <tr>
                                            <td>{{ $invitation->name_guest }}</td>
                                            <td>
                                                <div>{{ $invitation->email_guest }}</div>
                                                <div class="text-muted text-small">{{ $invitation->phone_guest }}</div>
                                            </td>
                                            <td>
                                                @if($invitation->rsvp_status == 'yes')
                                                <span class="badge badge-success">Attending</span>
                                                @elseif($invitation->rsvp_status == 'no')
                                                <span class="badge badge-danger">Not Attending</span>
                                                @elseif($invitation->rsvp_status == 'maybe')
                                                <span class="badge badge-info">Maybe</span>
                                                @else
                                                <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($invitation->plus_ones_count > 0)
                                                <span class="badge badge-primary">+{{ $invitation->plus_ones_count }}</span>
                                                @if($invitation->plus_ones_names)
                                                <div class="text-muted text-small">{{ $invitation->plus_ones_names }}</div>
                                                @endif
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>
                                                @if($invitation->dietary_preferences)
                                                <button type="button" class="btn btn-sm btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="{{ $invitation->dietary_preferences }}">
                                                    <i class="fas fa-utensils"></i>
                                                </button>
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>
                                                @if($invitation->rsvp_responded_at)
                                                {{ date('d M Y H:i', strtotime($invitation->rsvp_responded_at)) }}
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>
                                                @if($invitation->rsvp_notes)
                                                <button type="button" class="btn btn-sm btn-icon btn-primary" data-toggle="tooltip" data-placement="top" title="{{ $invitation->rsvp_notes }}">
                                                    <i class="fas fa-comment"></i>
                                                </button>
                                                @else
                                                -
                                                @endif
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
        $('#rsvp-table').DataTable({
            "order": [[5, "desc"]]
        });
        
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush 