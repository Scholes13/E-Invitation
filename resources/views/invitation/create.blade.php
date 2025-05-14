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
                        <input class="form-control" name="name" value="{{ old('name') }}" type="text" required>
                        @error('name')
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="">Email *</label>
                        <input class="form-control" name="email" value="{{ old('email') }}" type="email" required>
                        @error('email')
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="">Nomor Telepon *</label>
                        <input class="form-control" name="phone" value="{{ old('phone') }}" type="text" required>
                        @error('phone')
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="">Alamat</label>
                        <textarea class="form-control" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                      <div class="form-group">
                        <label for="">Perusahaan</label>
                        <input class="form-control" name="company" value="{{ old('company') }}" type="text">
                        @error('company')
                          <small class="text-danger"> {{ $message }} </small>
                        @enderror
                      </div>
                    </div>
                    <div class="col-xl-6">
                      <div class="form-group">
                        <label for="">Jenis Tamu *</label>
                        <select class="form-control" name="type" id="" required>
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
@endsection
