@extends('template.template')
@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Dashboard</h1>
    </div>
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-primary">
            <i class="fas fa-envelope"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Undangan</h4>
            </div>
            <div class="card-body">
              {{ $totalInvitation }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-success">
            <i class="fas fa-arrow-up"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Tamu Datang</h4>
            </div>
            <div class="card-body">
              {{ $totalGuestCome }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-warning">
            <i class="fas fa-arrow-down"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Tamu Belum Datang</h4>
            </div>
            <div class="card-body">
              {{ $totalGuestNotYet }}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <div class="card card-statistic-1">
          <div class="card-icon bg-info">
            <i class="fas fa-gift"></i>
          </div>
          <div class="card-wrap">
            <div class="card-header">
              <h4>Souvenir Diambil</h4>
            </div>
            <div class="card-body">
              {{ $totalSouvenirClaimed }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-5 col-md-12 col-12 col-sm-12">
        <form method="post" class="needs-validation" novalidate="">
          <div class="card">
            <div class="card-header">
              <h4>Acara</h4>
            </div>
            <div class="card-body pb-0">
              <div class="form-group">
                <label for="">Judul Acara</label>
                <input disabled class="form-control"  value="{{ myEvent()->type_event  }}" type="text">
              </div>
              <div class="form-group">
                <label for="">Nama Acara</label>
                <input disabled class="form-control"  value="{{  myEvent()->name_event  }}" type="text">
              </div>
              <div class="form-group">
                <label for="">Tanggal</label>
                <input disabled class="form-control" value="{{ \Carbon\Carbon::parse(myEvent()->start_event)->isoFormat('dddd, DD MMMM YYYY')  }}" type="text">
              </div>
              <div class="form-group">
                <label for="">Waktu</label>
                <input disabled class="form-control"  value="{{ \Carbon\Carbon::parse(myEvent()->start_event)->isoFormat('hh:mm a') . ' - ' . \Carbon\Carbon::parse(myEvent()->end_event)->isoFormat('hh:mm a') }}" type="text">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="col-lg-7 col-md-12 col-12 col-sm-12">
        <div class="card">
          <div class="card-header">
            <h4>Log Kedatangan</h4>
            <div class="card-header-action">
              <a href="{{ url('/arrival-log') }}" class="btn btn-secondary btn-sm">Lihat Lainnya <i class="fa fa-arrow-right"></i></a>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th>Tamu</th>
                    <th>Jenis</th>
                    <th>Meja</th>
                    <th>Datang</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($guestArrivals as $guest)
                  <tr>
                    <td>{{ $guest->name_guest }}</td>
                    <td>{{ strtoupper($guest->type_invitation) }}</td>
                    <td>{{ $guest->table_number_invitation }}</td>
                    <td>{{ $guest->checkin_invitation }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
