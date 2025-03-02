@extends('template.template')
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>User</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">User</div>
      </div>
    </div>

    <div class="section-body">
      <h2 class="section-title">Data User</h2>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
             <a class="btn btn-sm btn-primary" href="{{ url('user/create') }}"><i class="fa fa-plus"></i> Tambah User</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped" id="table-1">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama</th>
                      <th>Username</th>
                      <th>Email</th>
                      <th>Role</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($users as $key => $user)
                    <tr>
                      <td>{{ $key+1 }}</td>
                      <td>{{ $user->name }}</td>
                      <td>{{ $user->username }}</td>
                      <td>{{ $user->email }}</td>
                      <td>{{ $user->role == 1 ? "Admin" : "Resepsionis" }}</td>
                      <td class="text-center">
                        <a data-toggle="tooltip" data-placement="top" data-original-title="Edit" class="btn btn-sm btn-primary" href="{{ url('user/edit/'. $user->id) }}"><i class="fa fa-pencil-alt"></i></a>
                        <form action="{{ url('user/delete') }}" method="POST" class="d-inline" id="del-{{ $user->id_user }}">
                          @method("DELETE")
                          @csrf
                          <input type="hidden" name="id" value="{{ $user->id }}">
                          @if ($user->id != 1)
                          <button data-toggle="tooltip" data-placement="top" data-original-title="Hapus" class="btn btn-danger btn-sm" data-confirm="Hapus Data|Anda yakin hapus data ini?" data-confirm-yes="$('#del-{{ $user->id_user }}').submit()">
                            <i class="fas fa-trash"></i>
                          </button>                           
                          @endif
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
