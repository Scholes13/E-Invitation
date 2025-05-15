@extends('template.template')

@section('content')
<div class="main-content">
  <section class="section">
    <div class="section-header">
      <h1>Doorprize Generator</h1>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible show fade">
      <div class="alert-body">
        <button class="close" data-dismiss="alert">
          <span>×</span>
        </button>
        {{ session('success') }}
      </div>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible show fade">
      <div class="alert-body">
        <button class="close" data-dismiss="alert">
          <span>×</span>
        </button>
        {{ session('error') }}
      </div>
    </div>
    @endif

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4>Pilih Metode Doorprize</h4>
          </div>
          <div class="card-body">
            <div class="row justify-content-center">
              <div class="col-md-4 mb-4">
                <div class="card h-100 border-primary text-center">
                  <div class="card-body">
                    <div class="mb-3">
                      <i class="fas fa-dharmachakra fa-4x text-primary"></i>
                    </div>
                    <h4 class="card-title">Spin Wheel</h4>
                    <p class="card-text">Undian doorprize dengan putaran roda yang berwarna</p>
                    <a href="{{ route('doorprize.wheel') }}" class="btn btn-primary">Pilih Metode Ini</a>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4 mb-4">
                <div class="card h-100 border-info text-center">
                  <div class="card-body">
                    <div class="mb-3">
                      <i class="fas fa-dice fa-4x text-info"></i>
                    </div>
                    <h4 class="card-title">Slot Machine</h4>
                    <p class="card-text">Undian doorprize dengan animasi slot machine</p>
                    <a href="{{ route('doorprize.slots') }}" class="btn btn-info">Pilih Metode Ini</a>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4 mb-4">
                <div class="card h-100 border-success text-center">
                  <div class="card-body">
                    <div class="mb-3">
                      <i class="fas fa-random fa-4x text-success"></i>
                    </div>
                    <h4 class="card-title">Random Pick</h4>
                    <p class="card-text">Undian doorprize dengan acak nama sederhana</p>
                    <a href="{{ route('doorprize.random') }}" class="btn btn-success">Pilih Metode Ini</a>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="row mt-4">
              <div class="col-md-12">
                <div class="card bg-light">
                  <div class="card-header">
                    <h4>Pemenang Sebelumnya</h4>
                    <div class="card-header-action">
                      <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#resetModal">
                        <i class="fas fa-trash"></i> Reset Pemenang
                      </button>
                    </div>
                  </div>
                  <div class="card-body">
                    <div id="previous-winners" class="previous-winners">
                      @if(count($winners) > 0)
                        @foreach($winners as $index => $winner)
                        <div class="winner-item">
                          <strong>{{ $index + 1 }}. {{ $winner->name }}</strong>
                          <small class="text-muted d-block">{{ $winner->info }}</small>
                          <small class="text-muted">{{ date('d M Y H:i:s', strtotime($winner->created_at)) }}</small>
                        </div>
                        @endforeach
                      @else
                        <p class="text-muted">Belum ada pemenang</p>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Reset Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resetModalLabel">Konfirmasi Reset</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin mereset semua data pemenang doorprize? Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <form action="{{ route('doorprize.reset') }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-danger">Reset Pemenang</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .previous-winners {
    max-height: 300px;
    overflow-y: auto;
  }
  .winner-item {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
  }
  .winner-item:last-child {
    border-bottom: none;
  }
</style>
@endpush