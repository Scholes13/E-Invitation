@extends('template.template')

@section('content')
<div class="container">
   <h1 class="text-center">Door Prize Draw</h1>
   <div class="text-center mt-4">
       <button id="draw-button" class="btn btn-primary">Start Draw</button>
       <div id="winner-display" class="text-success mt-4">
           <h3>Drawing...</h3>
           <h2 id="spinning-name" class="mt-2"></h2>
       </div>
       <div id="past-winners" class="mt-4">
           <h4>Previous Winners</h4>
           <ul id="winners-list" class="list-unstyled">
               @foreach($guests as $guest)
                   <li>{{ $guest->name }}</li>
               @endforeach
           </ul>
       </div>
   </div>
</div>

<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" name="enable_rsvp" class="custom-control-input" id="enable_rsvp" {{ mySetting()->enable_rsvp == 1 ? 'checked' : '' }}>
        <label class="custom-control-label" for="enable_rsvp">Enable RSVP Feature</label>
    </div>
</div>

<div class="form-group">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" name="enable_custom_qr" class="custom-control-input" id="enable_custom_qr" {{ mySetting()->enable_custom_qr == 1 ? 'checked' : '' }}>
        <label class="custom-control-label" for="enable_custom_qr">Enable Custom QR Design Feature</label>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
   const spinningName = document.getElementById('spinning-name');
   const drawButton = document.getElementById('draw-button');
   const winnersList = document.getElementById('winners-list');
   let guests = @json($guests);
   let isDrawing = false;

   function displayGuest(guest) {
       spinningName.textContent = `${guest.name}`;
   }

   function addWinner(winner) {
       const li = document.createElement('li');
       li.textContent = `${winner.name}`;
       winnersList.appendChild(li);
   }

   function drawWinner() {
       if (isDrawing) return;
       isDrawing = true;
       drawButton.disabled = true;
       
       fetch('/doorprize/draw', {
           method: 'POST',
           headers: {
               'Content-Type': 'application/json',
               'X-CSRF-TOKEN': '{{ csrf_token() }}',
           },
           body: JSON.stringify({})
       })
       .then(response => response.json())
       .then(data => {
           if (data.winner) {
               spinningName.textContent = `WINNER: ${data.winner.name}`;
               spinningName.style.color = '#28a745';
               addWinner(data.winner);
           }
       })
       .catch(error => {
           console.error('Error:', error);
           spinningName.textContent = 'Error selecting winner. Please try again.';
       })
       .finally(() => {
           isDrawing = false;
           drawButton.disabled = false;
       });
   }

   drawButton.addEventListener('click', function() {
       spinningName.style.color = 'white';
       drawWinner();
   });
});
</script>
@endsection
