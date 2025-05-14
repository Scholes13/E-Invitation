@extends('template.template')

@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Log Pengambilan Souvenir</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item">Log Souvenir</div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Data Pengambilan Souvenir</h4>
                            <div class="card-header-action">
                                <a href="{{ route('souvenir.export') }}" class="btn btn-icon btn-primary">
                                    <i class="fas fa-file-export"></i> Export
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="souvenir-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Tamu</th>
                                            <th>QR Code</th>
                                            <th>Waktu Pengambilan</th>
                                            <th class="text-center">Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invitations as $key => $invitation)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $invitation->name_guest }}</td>
                                            <td>{{ $invitation->qrcode_invitation }}</td>
                                            <td>{{ \Carbon\Carbon::parse($invitation->souvenir_claimed_at)->format('d M Y H:i:s') }}</td>
                                            <td class="text-center">
                                                @if($invitation->souvenir_claimed_img)
                                                    @if(file_exists(public_path('img/scan/souvenir/' . $invitation->souvenir_claimed_img)))
                                                        <a href="{{ url('img/scan/souvenir/' . $invitation->souvenir_claimed_img) }}" target="_blank">
                                                            <img src="{{ url('img/scan/souvenir/' . $invitation->souvenir_claimed_img) }}" alt="Foto Pengambilan" class="img-thumbnail" style="max-width: 80px;">
                                                        </a>
                                                    @else
                                                        <span class="badge badge-warning">Foto tidak tersedia</span>
                                                    @endif
                                                @else
                                                    <span class="badge badge-light">Tidak ada foto</span>
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
        $('#souvenir-table').DataTable({
            "pageLength": 25,
            "order": [[ 3, "desc" ]]
        });
    });
</script>
@endpush

@push('styles')
<style>
    .img-thumbnail {
        transition: transform 0.2s;
    }
    .img-thumbnail:hover {
        transform: scale(1.5);
    }
</style>
@endpush 