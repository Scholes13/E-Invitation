@extends('template.template')
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Scan Manual</h1>
      <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
        <div class="breadcrumb-item">Manual</div>
      </div>
    </div>

    <div class="section-body">
      <h2 class="section-title">Data Undangan Tamu</h2>

      <div class="card">
        {{-- <div class="card-header"></div> --}}
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped" id="table-1">
              <thead>
                <tr>
                  <th>#</th>
                  <th>QrCode</th>
                  <th>Nama Tamu</th>
                  <th>No Meja</th>
                  <th>Jenis Tamu</th>
                  <th>Datang</th>
                  <th>Pulang</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($invitations as $key => $invitation)
                <tr>
                  <td>{{ $key+1 }}</td>
                  <td>{{ $invitation->qrcode_invitation }}</td>
                  <td>{{ $invitation->name_guest }}</td>
                  <td>{{ $invitation->table_number_invitation }}</td>
                  <td>{{ ucfirst($invitation->type_invitation) }}</td>
                  <td><?= $invitation->checkin_invitation == "" ? "<span class='text-danger'>Belum Datang</span>" : $invitation->checkin_invitation ?></td>
                  <td><?= $invitation->checkout_invitation == "" ? "-" : $invitation->checkout_invitation ?></td>
                  <td class="text-center">
                      {{-- <button {{ $invitation->checkout_invitation == null ? "" : "disabled" }} onclick="return confirm('Scan manual')" 
                        data-toggle="tooltip" data-placement="top" data-original-title="Scan Manual" 
                        class="btn btn-sm btn-info" type="submit"><i class="fa fa-cog"></i></button> --}}
                      <button {{ $invitation->checkout_invitation == null ? "" : "disabled" }} id="btn-scan" class="btn btn-dark btn-sm" 
                        data-toggle="modal" data-target="#modal-scan" 
                        data-id="{{ $invitation->id_invitation }}" 
                        data-come="{{ $invitation->checkin_invitation == null ? "0" : "1" }}"><i class="fas fa-qrcode"></i> Scan</button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<div class="modal fade" id="modal-scan" tabindex="-1" aria-labelledby="ModalScan" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalScan">Konfirmasi Scan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <span>Undangan tamu akan di-scan manual</span>
      </div>
      <div class="modal-footer">
        <form action="{{ url('/arrived-manually/process-scan/') }}" method="POST">
          @csrf
          @method('PUT')
          <input type="hidden" name="id" id="id">
          <input type="hidden" name="come" id="come">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-icon icon-left">
            <i class="fas fa-paper-plane"></i> Proses Scan
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).on('click', '#btn-scan', function() {
    let id = $(this).data("id")
    let come = $(this).data("come")
    $('#id').val(id);
    $('#come').val(come);
  })
</script>

@endsection
