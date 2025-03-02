@extends('template.template')
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Tamu</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">Guest</div>
        <div class="breadcrumb-item">Add</div>
      </div>
    </div>

    <div class="section-body">
      <h2 class="section-title">Tambah Tamu</h2>

      <div class="card">
        <div class="card-header">
          <h4 style="font-weight:normal; font-size:14px;">* required</h4>
          <div class="card-header-action">
            <a class="btn btn-sm btn-secondary" href="{{ url('guest') }}"><i class="fa fa-arrow-left"></i> Kembali</a>
          </div>
        </div>
        <div class="card-body">
          <form action="{{ url('guest/store') }}" method="POST" autocomplete="off">
            @method('POST')
            @csrf
            <div class="row">
              <div class="col-xl-6">
                <div class="form-group">
                  <label for="">Nama Tamu *</label>
                  <input class="form-control" name="name" value="{{ old('name') }}" type="text" autofocus>
                  @error('name')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="">ID Identitas</label>
                  <input class="form-control" name="nik" value="{{ old('nik') }}" type="text">
                  @error('nik')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="">Email *</label>
                  <input class="form-control" name="email" value="{{ old('email') }}" type="text">
                  @error('email')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="">No. Telepon *</label>
                  <input class="form-control" name="phone" value="{{ old('phone') }}" type="text" placeholder="ex. 6281225764094">
                  @error('phone')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="">Alamat</label>
                  <input class="form-control" name="address" value="{{ old('address') }}" type="text">
                  @error('address')
                    <small class="text-danger"> {{ $message }} </small>
                  @enderror
                </div>
                <div class="form-group">
                  <label for="">Keterangan Tamu</label>
                  <input class="form-control" name="information" value="{{ old('information') }}" type="text" placeholder="">
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
                <div class="form-group">
                  <button class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
          
    </div>
  </section>
</div>

@endsection
