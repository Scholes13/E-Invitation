@extends('template.template')
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Log Kedatangan</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">Log Kedatangan</div>
      </div>
    </div>

    <div class="section-body">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4>Data Kedatangan Tamu</h4>
              <div class="card-header-action">
                <div class="dropdown d-inline mr-2">
                  <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-filter"></i> Filter
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <div class="dropdown-title">Filter Data</div>
                    <form action="{{ url('arrival-log') }}" method="GET">
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
                      <a href="{{ url('arrival-log') }}" class="dropdown-item has-icon text-danger">
                        <i class="fas fa-times"></i> Reset Filter
                      </a>
                    </form>
                  </div>
                </div>
                <a href="{{ url('arrival-log/export') . $paramsUrl }}" class="btn btn-primary mr-2">
                  <i class="fas fa-file-export"></i> Export
                </a>
                <form action="{{ url('/arrival-log/delete-all') }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua data kedatangan?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger">
                    <i class="fa fa-trash"></i> Hapus Data
                  </button>
                </form>
              </div>
            </div>
            <div class="card-body">
              <!-- Tabel Data Tamu -->
              <div class="table-responsive">
                <table class="table table-striped" id="attendance-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>QR Code</th>
                      <th>Nama Tamu</th>
                      <th>Jenis Tamu</th>
                      <th>No Meja</th>
                      <th>Waktu Datang</th>
                      <th>Waktu Pulang</th>
                      <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($invt as $key => $invt)
                    <tr>
                      <td>{{ $key+1 }}</td>
                      <td>{{ $invt->qrcode_invitation }}</td>
                      <td>{{ $invt->name_guest }}</td>
                      <td><span class="badge badge-{{ $invt->type_invitation == 'vip' ? 'primary' : 'info' }}">{{ strtoupper($invt->type_invitation) }}</span></td>
                      <td>{{ $invt->table_number_invitation }}</td>
                      <td>{{ \Carbon\Carbon::parse($invt->checkin_invitation)->format('d M Y H:i:s') }}</td>
                      <td>{{ $invt->checkout_invitation ? \Carbon\Carbon::parse($invt->checkout_invitation)->format('d M Y H:i:s') : '-' }}</td>
                      <td class="text-center">
                        <a href="{{ url('arrival-log/'. $invt->id_invitation) }}" class="btn btn-primary btn-sm">
                          <i class="fas fa-info-circle"></i> Detail
                        </a>
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
    $('#attendance-table').DataTable({
      "pageLength": 25,
      "order": [[ 5, "desc" ]]
    });
  });
</script>
@endpush

@push('styles')
<style>
  .badge {
    padding: 5px 8px;
    font-weight: 500;
  }
  .badge-primary {
    background-color: #3f51b5;
  }
  .badge-info {
    background-color: #00bcd4;
  }
  .btn-group .btn {
    margin-left: 5px;
  }
</style>
@endpush
