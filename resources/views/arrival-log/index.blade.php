@extends('template.template')
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Log Kedatangan</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">Attendance Log</div>
      </div>
    </div>

    <div class="section-body">
      <h2 class="section-title">Data Tamu Hadir</h2>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">

              <div class="mb-4">
                <form action="">
                  <div class="form-group">
                    <label for="">Filter</label>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <select class="form-control form-control-sm" name="type" style="height:36px; padding:0 10px;">
                            <option value="">- Jenis Tamu-</option>
                            <option value="reguler">REGULER</option>
                            <option value="vip">VIP</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div>
                          <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> Filter</button>
                          <a class="btn btn-dark" href="{{ url('arrival-log/export') . $paramsUrl }}"><i class="fa fa-file-export"></i> Export Excel</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>

              <!-- Tombol Hapus Semua -->
              <div class="mb-4">
                <form action="{{ url('/arrival-log/delete-all') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua data kedatangan?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-danger">
                    <i class="fa fa-trash"></i> Hapus Semua
                  </button>
                </form>
              </div>

              <!-- Tabel Data Tamu -->
              <div class="table-responsive">
                <table class="table table-hover" id="table-1">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>QrCode</th>
                      <th>Nama Tamu</th>
                      <th>Jenis Tamu</th>
                      <th>No Meja</th>
                      <th>Datang</th>
                      <th>Pulang</th>
                      <th>Detail</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($invt as $key => $invt)
                    <tr>
                      <td>{{ $key+1 }}</td>
                      <td>{{ $invt->qrcode_invitation }}</td>
                      <td>{{ $invt->name_guest }}</td>
                      <td>{{ strtoupper($invt->type_invitation) }}</td>
                      <td>{{ $invt->table_number_invitation }}</td>
                      <td>{{ $invt->checkin_invitation }}</td>
                      <td>{{ $invt->checkout_invitation }}</td>
                      <td class="text-center">
                        <a data-toggle="tooltip" data-placement="top" data-original-title="Detail" class="btn btn-sm btn-primary" href="{{ url('arrival-log/'. $invt->id_invitation) }}"><i class="fa fa-info-circle"></i></a>
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
