@extends('template.scan') <!-- Ganti dengan template yang Anda gunakan -->

@section('content')
<div class="container">
    <h1>Undian Doorprize</h1>
    
    <h3>Daftar Tamu yang Diundang</h3>
    <ul id="guest-list">
        @foreach($guests as $guest)
            <li>
                {{ $guest->name_guest }} - {{ $guest->invitation->information_invitation }}
            </li>
        @endforeach
    </ul>

    <button id="draw-winner">Undi Pemenang</button>

    <div id="winner" style="display: none;">
        <strong>Pemenang: </strong><span id="winner-name"></span>
    </div>
</div>

<script>
    document.getElementById('draw-winner').addEventListener('click', function() {
        fetch('{{ route('doorprize.draw') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Semua tamu sudah menjadi pemenang.');
            }
            return response.json();
        })
        .then(data => {
            document.getElementById('winner-name').innerText = data.winner.name_guest;
            document.getElementById('winner').style.display = 'block'; // Menampilkan pemenang
        })
        .catch(error => {
            alert(error.message);
        });
    });
</script>

@endsection