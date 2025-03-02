@extends('template.scan')
@section('content')
<title>Registrasi - {{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title>
<div class="container">
  <div class="row justify-content-center mt-4">
    <div class="col-md-8 col-xl-6">
      <div class="card mt-5">
        <div class="card-header d-block text-center">
          <h4>Registrasi Acara - {{ mySetting()->name_app }}</h4>
        </div>
        <div class="card-body">
          <form id="form-register" action="{{ url('register-process') }}" method="POST" autocomplete="off">
            @method('POST')
            @csrf
            <div class="row">
              <div class="col-12">
                <div class="form-group">
                  <label for="">Nama Lengkap *</label>
                  <input class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" type="text" autofocus>
                  @error('name')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="">ID Identitas</label>
                  <input class="form-control @error('nik') is-invalid @enderror" name="nik" value="{{ old('nik') }}" type="text" placeholder="NIK / Paspor / SIM / etc">
                  @error('nik')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="">Alamat</label>
                  <textarea class="form-control" name="address">{{ old('address') }}</textarea>
                </div>
                <div class="form-group">
                  <label for="">Email *</label>
                  <input class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" type="text">
                  @error('email')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="">No. Telepon *</label>
                  <input class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" type="text">
                  @error('phone')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="">Keterangan</label>
                  <textarea class="form-control" name="information" type="text">{{ old('information') }}</textarea>
                </div>
                <div>
                  <button type="button" id="simpan" class="btn btn-block btn-primary btn-lg"><i class="fa fa-save"></i> Daftar</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="simple-footer text-muted" style="color:#FFFF !important;">
        © 2024 Made with love by Pramuji. Powered by YukCoding Dev.
      </div>

    </div>
  </div>

</div>

<div class="modal fade" id="modal-validation" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Pastikan Data Sudah Benar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="">Nama Lengkap</label>
          <input id="valid-name" disabled class="form-control form-control-sm" value="" type="text">
        </div>
        <div class="form-group">
          <label for="">ID Identitas</label>
          <input id="valid-nik" disabled class="form-control form-control-sm" value=""  type="text">
        </div>
        <div class="form-group">
          <label for="">Alamat</label>
          <input id="valid-address" disabled class="form-control form-control-sm" value=""  type="text">
        </div>
        <div class="form-group">
          <label for="">Email</label>
          <input id="valid-email" disabled class="form-control form-control-sm" value="" type="text">
        </div>
        <div class="form-group">
          <label for="">No. Telepon</label>
          <input id="valid-phone" disabled class="form-control form-control-sm" value="" type="text">
        </div>
        <div class=">
          <label for="">Keterangan</label>
          <input id="valid-information" disabled class="form-control form-control-sm" value="" type="text">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button id="simpan-data" type="button" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){

    $("#simpan").click(function(){

      var name = $("input[name='name']").val();
      var nik = $("input[name='nik']").val();
      var address = $("textarea[name='address']").val();
      var email = $("input[name='email']").val();
      var phone = $("input[name='phone']").val();
      var information = $("textarea[name='information']").val();
      $("#valid-name").val(name)
      $("#valid-nik").val(nik)
      $("#valid-address").val(address)
      $("#valid-email").val(email)
      $("#valid-phone").val(phone)
      $("#valid-information").val(information)
      $("#modal-validation").modal('show');
    })

    $("#simpan-data").click(function(){
      $("#form-register").submit();
    })

  })
</script>

@endsection
