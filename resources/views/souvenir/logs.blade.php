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
                                <div class="dropdown d-inline mr-2">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <div class="dropdown-title">Filter Data</div>
                                        <form action="{{ route('souvenir.logs') }}" method="GET">
                                            <div class="form-group px-3">
                                                <label>Jenis Tamu</label>
                                                <select name="type" class="form-control">
                                                    <option value="">Semua</option>
                                                    <option value="vip" {{ isset($_GET['type']) && $_GET['type'] == 'vip' ? 'selected' : '' }}>VIP</option>
                                                    <option value="regular" {{ isset($_GET['type']) && $_GET['type'] == 'regular' ? 'selected' : '' }}>Regular</option>
                                                </select>
                                            </div>
                                            <div class="form-group px-3">
                                                <label>No Meja</label>
                                                <input type="text" class="form-control" name="table" value="{{ isset($_GET['table']) ? $_GET['table'] : '' }}" placeholder="Nomor Meja">
                                            </div>
                                            <div class="dropdown-divider"></div>
                                            <button type="submit" class="dropdown-item has-icon text-primary">
                                                <i class="fas fa-search"></i> Terapkan Filter
                                            </button>
                                            <a href="{{ route('souvenir.logs') }}" class="dropdown-item has-icon text-danger">
                                                <i class="fas fa-times"></i> Reset Filter
                                            </a>
                                        </form>
                                    </div>
                                </div>
                                <a href="{{ route('souvenir.export') }}{{ isset($_GET['type']) || isset($_GET['table']) ? '?type='.(isset($_GET['type']) ? $_GET['type'] : '').'&table='.(isset($_GET['table']) ? $_GET['table'] : '') : '' }}" class="btn btn-icon btn-primary">
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