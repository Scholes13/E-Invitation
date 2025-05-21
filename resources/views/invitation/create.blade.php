@extends('template.template')
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Undangan</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">Invitation</div>
        <div class="breadcrumb-item">Add</div>
      </div>
    </div>

    <div class="section-body">
      <h2 class="section-title">Tambah Undangan</h2>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h4 style="font-weight:normal; font-size:14px;">* required</h4>
              <div class="card-header-action">
                <a class="btn btn-sm btn-secondary" href="{{ url('invite') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
              </div>
            </div>
            <div class="card-body">
              <form action="{{ url('invite/store') }}" method="POST" autocomplete="off">
                @method('POST')
                @csrf
                  <div class="row">
                    <div class="col-xl-6">
                      <div class="form-group">
                        <label for="">Nama Tamu *</label>
                        <select class="select2 form-control" name="guest" id="">
                          <option value="">- Pilih -</option>
                          @foreach ($guests as $guest)
                          <option @if (old('guest') == $guest->id_guest) selected @endif value="{{ $guest->id_guest }}">{{ $guest->invitation == null ? $guest->name_guest : "* " . $guest->name_guest }}</option>
                          @endforeach
                        </select>
                        @error('guest')
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="">Jenis Tamu *</label>
                        <select class="form-control" name="type" id="">
                          <option value="">- Pilih -</option>
                          <option @if (old('type') == "reguler") selected @endif value="reguler">REGULER</option>
                          <option @if (old('type') == "vip") selected @endif value="vip">VIP</option>
                        </select>
                        @error('type')
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="">Nomor Meja</label>
                        <input class="form-control" name="table_number" value="{{ old('table_number') }}" type="text">
                        @error('table_number')
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="">Keterangan Undangan</label>
                        <input class="form-control" name="information" value="{{ old('information') }}" type="text">
                        @error('information') 
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="">Pesan Khusus</label>
                        <textarea class="form-control" name="custom_message" rows="4">{{ old('custom_message') }}</textarea>
                        @error('custom_message')
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                    <div class="col-xl-6">
                      <div class="form-group">
                        <label for="">ID Identitas</label>
                        <input id="nik" class="form-control" type="text" readonly value="">
                      </div>
                      <div class="form-group">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="">Email</label>
                            <input id="email" class="form-control" type="text" readonly value="">
                          </div>
                          <div class="col-md-6">
                            <label for="">Telp</label>
                            <input id="telp" class="form-control" type="text" readonly value="">
                          </div>
                        </div>
                        
                      </div>
                      <div class="form-group">
                        <label for="">Alamat</label>
                        <input id="alamat" class="form-control" type="text" readonly value="">
                      </div>
                      <div class="form-group">
                        <label for="">Keterangan Tamu</label>
                        <input id="ket" class="form-control" type="text" readonly value="">
                      </div>
                    </div>
                    <div class="col">
                      <div class="form-group">
                        <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                      </div>
                    </div>
                  </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  $(document).ready(function(){
    
    function getGuest()
    {
      $.ajax({
        url     : "{{ url('invite/get-guest') }}",
        method  : "GET",
        type    : "JSON",
        data    : {
          id_guest :  $("select[name='guest']").val()
        },
        success : (res) => {
          var data = res.data;
          if(data){
            $("#nik").val(data.nik_guest)
            $("#email").val(data.email_guest)
            $("#telp").val(data.phone_guest)
            $("#alamat").val(data.address_guest)
            $("#ket").val(data.information_guest)
          }
        },
        error   : () => {;
          console.log("failed get data")
        }
        })
    }

    getGuest();

    $("select[name='guest']").change(function(){
        getGuest();
    })

  })
</script>

@endsection
