img/@extends('template.template')
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Log Kedatangan</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">Attendance Log</div>
        <div class="breadcrumb-item">Detail</div>
      </div>
    </div>

    <div class="section-body">
      <h2 class="section-title">Detail Tamu Datang</h2>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4></h4>
              <div class="card-header-action">
                <a class="btn btn-sm btn-secondary float-right" href="{{ url('arrival-log') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-xl-6">
                  <div class="form-group">
                    <label for="">QrCode</label>
                    <input class="form-control" type="text" value="{{ $invt->qrcode_invitation }}" disabled>
                  </div>
                  <div class="form-group">
                    <label for="">Nama Tamu</label>
                    <input class="form-control" type="text" value="{{ $invt->name_guest }}" disabled>
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-6">
                        <label for="">Email</label>
                        <input id="email" class="form-control" type="text" disabled value="{{ $invt->email_guest }}">
                      </div>
                      <div class="col-md-6">
                        <label for="">Telp</label>
                        <input id="telp" class="form-control" type="text" disabled value="{{ $invt->phone_guest }}">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="">Alamat</label>
                    <input id="alamat" class="form-control" type="text" disabled value="{{ $invt->address_guest }}">
                  </div>
                  <div class="form-group">
                    <label for="">Keterangan Tamu</label>
                    <input id="ket" class="form-control" type="text" disabled value="{{ $invt->information_guest }}">
                  </div>
                </div>
                <div class="col-xl-6">
                  <div class="row">
                    <div class="form-group col-xl">
                      <label for="">Jenis Undangan</label>
                      <input class="form-control" type="text" value="{{ strtoupper($invt->type_invitation) }}" disabled>
                    </div>
                    <div class="form-group col-xl">
                      <label for="">No Meja</label>
                      <input class="form-control" value="{{ $invt->table_number_invitation }}" type="text" disabled>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="">Keterangan Undangan</label>
                    <input class="form-control" value="{{ $invt->information_invitation }}" type="text" disabled>
                  </div>
                  <div class="row">
                    <div class="form-group col-xl">
                      <label for="">Datang</label>
                      <input class="form-control" value="{{ $invt->checkin_invitation }}" type="text" disabled>
                      <div class="pt-2 text-center">
                        @if ($invt->checkin_img_invitation)
                          @if (file_exists(public_path('img/scan/scan-in/'. $invt->checkin_img_invitation)))
                            <img class="rounded" style="width: 100%" src="{{ url('img/scan/scan-in/'. $invt->checkin_img_invitation) }}" alt="">
                            <small>Gambar Scan In</small>
                          @endif
                        @endif
                      </div>
                    </div>
                    <div class="form-group col-xl">
                      <label for="">Pulang</label>
                      <input class="form-control" value="{{ $invt->checkout_invitation }}" type="text" disabled>
                      <div class="pt-2 text-center">
                        @if ($invt->checkout_img_invitation)
                          @if (file_exists(public_path('img/scan/scan-out/'. $invt->checkout_img_invitation)))
                            <img class="rounded" style="width: 100%" src="{{ url('img/scan/scan-out/'. $invt->checkout_img_invitation) }}" alt="">
                            <small>Gambar Scan Out</small>
                          @endif
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

@endsection
