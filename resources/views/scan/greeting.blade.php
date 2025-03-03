@extends('template.scan')
@section('content') 

    <title>Greetings - {{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <div class="container">
        <div class="content-wrapper" style="text-align: center; margin-top:5%; display: flex; flex-direction: column; justify-content: flex-start; height: 100vh; overflow: hidden;">
            <h1 style="color:#ddd;" id="intro"></h1>
            <h1 class="display-1" style="color:#fff; font-size: 60px;" id="guest"></h1>
            <h1 style="color:#ddd; font-size: 30px;" id="pesanku"></h1>
        </div>
    </div>

    <script>
        // Enable pusher logging - don't include this in production
        // Pusher.logToConsole = true;
        const pusher = new Pusher('7473c6ca3220f2454f50', {
            cluster: 'ap1'
        });

        const channel = pusher.subscribe('greetings');
        channel.bind('new-scan', function(data) {
            const intro = document.getElementById('intro');
            const guest = document.getElementById('guest');
            const pesanku = document.getElementById('pesanku');

            intro.innerHTML = `${data.intro}`;
            guest.innerHTML = `${data.guest}`;
            pesanku.innerHTML = data.custom_message || '';

            // Reset tampilan setelah beberapa detik
            setTimeout(() => {
                intro.innerHTML = '';
                guest.innerHTML = '';
                pesanku.innerHTML = '';
            }, 6000);
        });
    </script>

@endsection

<!-- CSS tambahan untuk memastikan tampilan penuh -->
<style>
    html, body {
        height: 100%;
        margin: 0;
        overflow: hidden; /* Menyembunyikan scroll jika ada konten yang meluber */
    }
</style>
