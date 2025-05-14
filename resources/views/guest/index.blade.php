@extends('template.template')
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Tamu & Undangan</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">Tamu & Undangan</div>
      </div>
    </div>

    <div class="section-body">
      <h2 class="section-title">Data Tamu & Undangan</h2>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
             <a class="btn btn-sm btn-primary" href="{{ url('guest/create') }}"><i class="fa fa-plus"></i> Tambah Tamu & Undangan</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped" id="table-1">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama</th>
                      <th>Email</th>
                      <th>Telp</th>
                      <th>Alamat</th>
                      <th>QR Code</th>
                      <th>Meja</th>
                      <th>Tipe</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($guests as $key => $guest)
                    @php
                      $invitation = $guest->invitation;
                    @endphp
                    <tr>
                      <td>{{ $key+1 }}</td>
                      <td>{{ $guest->name_guest }}</td>
                      <td>{{ $guest->email_guest }}</td>
                      <td>{{ $guest->phone_guest }}</td>
                      <td>{{ $guest->address_guest }}</td>
                      <td>
                        @if($invitation)
                          <a href="{{ url('/invitation/'.$invitation->qrcode_invitation) }}" target="_blank">
                            {{ $invitation->qrcode_invitation }}
                          </a>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>{{ $invitation ? $invitation->table_number_invitation : '-' }}</td>
                      <td>
                        @if($invitation)
                          <span class="badge badge-{{ $invitation->type_invitation == 'vip' ? 'warning' : 'info' }}">
                            {{ ucfirst($invitation->type_invitation) }}
                          </span>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td class="text-center">
                        <a data-toggle="tooltip" data-placement="top" data-original-title="Edit" class="btn btn-sm btn-primary" href="{{ url('guest/edit/'. $guest->id_guest) }}"><i class="fa fa-pencil-alt"></i></a>
                        @if($invitation)
                          <a data-toggle="tooltip" data-placement="top" data-original-title="Download QR" class="btn btn-sm btn-success" href="{{ url('download/'. $invitation->qrcode_invitation) }}"><i class="fa fa-download"></i></a>
                        @endif
                        <form action="{{ url('guest/delete') }}" method="POST" class="d-inline" id="del-{{ $guest->id_guest }}">
                          @method("DELETE")
                          @csrf
                          <input type="hidden" name="id_guest" value="{{ $guest->id_guest }}">
                          <button data-toggle="tooltip" data-placement="top" data-original-title="Hapus" class="btn btn-danger btn-sm" data-confirm="Hapus Data|Anda yakin hapus data ini?" data-confirm-yes="$('#del-{{ $guest->id_guest }}').submit()">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
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
