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
                  <label for="name">Nama Lengkap *</label>
                  <input 
                    class="form-control @error('name') is-invalid @enderror" 
                    name="name" 
                    value="{{ old('name') }}" 
                    type="text" 
                    autofocus
                  >
                  @error('name')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                
                <div class="form-group">
                  <label for="company">Company</label>
                  <input 
                    class="form-control @error('company') is-invalid @enderror" 
                    name="company" 
                    value="{{ old('company') }}" 
                    type="text" 
                    placeholder="Company"
                  >
                  @error('company')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                
                <div class="form-group">
                  <label for="address">Alamat</label>
                  <textarea 
                    class="form-control" 
                    name="address"
                  >{{ old('address') }}</textarea>
                </div>
                
                <div class="form-group">
                  <label for="email">Email *</label>
                  <input 
                    class="form-control @error('email') is-invalid @enderror" 
                    name="email" 
                    value="{{ old('email') }}" 
                    type="text"
                  >
                  @error('email')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                
                <div class="form-group">
                  <label for="phone">No. Telepon *</label>
                  <input 
                    class="form-control @error('phone') is-invalid @enderror" 
                    name="phone" 
                    value="{{ old('phone') }}" 
                    type="text"
                  >
                  @error('phone')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                
                <div class="d-grid gap-2">
                  <!-- Tombol pemicu modal -->
                  <button 
                    type="button" 
                    id="simpan" 
                    class="btn btn-block btn-primary btn-lg"
                  >
                    <i class="fa fa-save"></i> Daftar
                  </button>
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

<!-- Modal Konfirmasi -->
<div 
  class="modal fade" 
  id="modal-validation" 
  tabindex="-1" 
  aria-labelledby="staticBackdropLabel"
  aria-hidden="true"
>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Pastikan Data Sudah Benar</h5>
        <button 
          type="button" 
          class="close" 
          data-dismiss="modal" 
          aria-label="Close"
        >
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <div class="form-group">
          <label for="valid-name">Nama Lengkap</label>
          <input 
            id="valid-name" 
            disabled 
            class="form-control form-control-sm" 
            value="" 
            type="text"
          >
        </div>
        
        <div class="form-group">
          <label for="valid-company">Company</label>
          <input 
            id="valid-company" 
            disabled 
            class="form-control form-control-sm" 
            value=""  
            type="text"
          >
        </div>
        
        <div class="form-group">
          <label for="valid-address">Alamat</label>
          <input 
            id="valid-address" 
            disabled 
            class="form-control form-control-sm" 
            value=""  
            type="text"
          >
        </div>
        
        <div class="form-group">
          <label for="valid-email">Email</label>
          <input 
            id="valid-email" 
            disabled 
            class="form-control form-control-sm" 
            value="" 
            type="text"
          >
        </div>
        
        <div class="form-group">
          <label for="valid-phone">No. Telepon</label>
          <input 
            id="valid-phone" 
            disabled 
            class="form-control form-control-sm" 
            value="" 
            type="text"
          >
        </div>
      </div>
      
      <div class="modal-footer">
        <button 
          type="button" 
          class="btn btn-secondary" 
          data-dismiss="modal"
        >
          Batal
        </button>
        <button 
          id="simpan-data" 
          type="button" 
          class="btn btn-primary"
        >
          Simpan
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function(){
    
    // Ketika tombol "Daftar" (id="simpan") diklik
    $("#simpan").click(function(){
      console.log("Simpan button clicked"); // Debugging line
      var name = $("input[name='name']").val();
      var company = $("input[name='company']").val();
      var address = $("textarea[name='address']").val();
      var email = $("input[name='email']").val();
      var phone = $("input[name='phone']").val();
      
      // Isi field di dalam modal
      $("#valid-name").val(name);
      $("#valid-company").val(company);
      $("#valid-address").val(address);
      $("#valid-email").val(email);
      $("#valid-phone").val(phone);
      
      // Tampilkan modal
      $("#modal-validation").modal('show');
    });

    // Ketika tombol "Simpan" di modal diklik
    $("#simpan-data").click(function(){
      console.log("Simpan-data button clicked"); // Debugging line
      // Submit form aslinya
      $("#form-register").submit();
    });

  });
</script>
@endsection
