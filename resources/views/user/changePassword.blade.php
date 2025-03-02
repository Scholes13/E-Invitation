@extends('template.template')
@section('content')
<div class="main-content" style="min-height: 847px;">
  <section class="section">
    <div class="section-header">
      <h1>Profile</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
        <div class="breadcrumb-item">Profile</div>
        <div class="breadcrumb-item">Password</div>
      </div>
    </div>

    <div class="section-body">
      <h2 class="section-title">Ubah Password Akun</h2>
      <div class="row">
        <div class="col-12 col-sm-12 col-lg-6">
          <div class="card author-box card-secondary">
            <div class="card-body">
              <form action="{{ url('change-password-process') }}" method="POST">
                @csrf
                <div class="row">
                  <div class="form-group col-12">
                    <label for="">Password Lama</label>
                    <input class="form-control" type="password" name="old_pass" required>
                  </div>
                  <div class="form-group col-12">
                    <label for="">Password Baru</label>
                    <input class="form-control" type="password" name="new_pass" required>
                  </div>
                  <div class="form-group col-12">
                    <label for="">Konfirmasi Password Baru</label>
                    <input class="form-control" type="password" name="pass_conf" required>
                  </div>
                </div>
                <div>
                  <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan Password</button>
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