@extends('template.template')
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Acara</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Event</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Setting Acara</h2>

				<div class="card">
					<div class="card-body">
						<form action="{{ url('event/set') }}" method="POST" enctype="multipart/form-data"
							autocomplete="off">
							@method('PUT')
							@csrf
							<div class="row">
								<div class="col-xl-6">
									<div class="form-group">
										<label for="">Judul Acara *</label>
										<input name="type" type="text" value="{{ old('type', $event->type_event) }}" class="form-control @error('type') is-invalid @enderror">
										@error('type')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
									</div>
									<div class="form-group">
										<label for="">Nama Acara *</label>
										<textarea class="form-control @error('name') is-invalid @enderror" name="name" rows="2" style="height:auto;">{{ old('name', $event->name_event) }}</textarea>
										@error('name')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
									</div>
									<div class="form-group">
										<label for="">Tempat Acara *</label>
										<input class="form-control @error('place') is-invalid @enderror" name="place" value="{{ old('place', $event->place_event) }}" type="text">
										@error('place')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
									</div>
									<div class="form-group">
										<label for="">Alamat Acara *</label>
										<input class="form-control @error('location') is-invalid @enderror" name="location" value="{{ old('location', $event->location_event) }}" type="text">
										@error('location')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
									</div>
									<div class="form-group">
										<label for="">Mulai Acara *</label>
										<input class="form-control @error('start') is-invalid @enderror" name="start" value="{{ old('location', $event->start_event) }}" type="datetime-local">
										@error('start')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
									</div>
									<div class="form-group">
										<label for="">Selesai Acara *</label>
										<input class="form-control @error('end') is-invalid @enderror" name="end" value="{{ old('location', $event->end_event) }}" type="datetime-local">
										@error('end')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
									</div>
									<div class="form-group">
										<label for="">Ucapan Undangan (Bawah)</label>
										<textarea class="form-control" name="information" rows="2" style="height:auto;">{{ old('information', $event->information_event) }}</textarea>
									</div>
									<div class="form-group">
										<label for="">Gambar Undangan (Atas)</label>
										<label>
											<input type="checkbox" name="image_top_status" value="1" class="custom-switch-input" @if ($event->image_top_status == 1) checked @endif>
											<span class="custom-switch-indicator"></span>
											{{-- <span class="custom-switch-description">Show</span> --}}
										</label>
										<input type="file" name="image" value="{{ $event->image_event }}" class="form-control @error('image') is-invalid @enderror" 
											onchange="document.getElementById('imagePreview').src = window.URL.createObjectURL(this.files[0])">
										@error('image')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
										@php
											if ($event->image_event != '') {
												if (file_exists(public_path('img/event/' . $event->image_event))) {
													$img = asset('img/event/' . $event->image_event);
												} else {
													$img = asset('asset/front/image-not-found.jpg');
												}
											} else {
												$img = asset('asset/front/default.png');
											}
										@endphp
										<img id="imagePreview" src="{{ $img }}" alt="" style="width:150px; margin-top:10px; border:0px solid;" />
									</div>
								</div>
								<div class="col-xl-6">
									<div class="form-group">
										<label>Warna Text Undangan</label>
										<input class="form-control" name="color_text_event" type="color" value="{{ old('color_text_event', $event->color_text_event) }}">
									</div>
									<div class="form-group">
										<label>Gambar Background Undangan</label>
										<label>
											<input type="checkbox" name="image_bg_status" value="1" class="custom-switch-input" @if ($event->image_bg_status == 1) checked @endif>
											<span class="custom-switch-indicator"></span>
										</label>
										<input type="file" name="image_bg_event" class="form-control @error('image_bg_event') is-invalid @enderror" 
											onchange="document.getElementById('imagePreviewBg').src = window.URL.createObjectURL(this.files[0])">
										@error('image_bg_event')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
										<img id="imagePreviewBg" src="{{ asset('img/event/'.$event->image_bg_event) }}" onerror="this.style.display='none'" onload="this.style.display='block'" style="width:250px; margin-top:10px; border:0px solid;" />
									</div>
									<div class="form-group">
										<label>Warna Background Undangan</label>
										<input class="form-control" name="color_bg_event" type="color" value="{{ old('color_bg_event', $event->color_bg_event) }}">
									</div>

									<div class="form-group">
										<label>Gambar Undangan (Kiri)</label>
										<label>
											<input type="checkbox" name="image_left_status" value="1" class="custom-switch-input" @if ($event->image_left_status == 1) checked @endif>
											<span class="custom-switch-indicator"></span>
										</label>
										<input type="file" name="image_left_event" class="form-control @error('image_left_event') is-invalid @enderror" 
											onchange="document.getElementById('imagePreviewLeft').src = window.URL.createObjectURL(this.files[0]);">
										@error('image_left_event')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
										<img id="imagePreviewLeft" src="{{ asset('img/event/'.$event->image_left_event) }}" onerror="this.style.display='none'" onload="this.style.display='block'" style="width:200px; margin-top:10px; border:0px solid;" />
									</div>
									<div class="form-group">
										<label>Gambar Undangan (Kanan)</label>
										<label>
											<input type="checkbox" name="image_right_status" value="1" class="custom-switch-input" @if ($event->image_right_status == 1) checked @endif>
											<span class="custom-switch-indicator"></span>
										</label>
										<input type="file" name="image_right_event" class="form-control @error('image_right_event') is-invalid @enderror" 
											onchange="document.getElementById('imagePreviewRight').src = window.URL.createObjectURL(this.files[0])">
										@error('image_right_event')
										<small class="text-danger"> {{ $message }} </small>
										@enderror
										<img id="imagePreviewRight" src="{{ asset('img/event/'.$event->image_right_event) }}" onerror="this.style.display='none'" onload="this.style.display='block'" style="width:200px; margin-top:10px; border:0px solid;" />
									</div>

									<div class="text-right">
										<button class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Simpan</button>
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
