@extends('template.scan')
@section('content')

<title>Enter Code - {{ mySetting()->name_app != '' ? mySetting()->name_app : env('APP_NAME') }}</title>

<div class="container pt-5">
    <div class="form-group mt-5">
        <h2 class="text-light text-center">Enter Code</h2>
        <form id="codeInputForm">
            <div class="d-flex justify-content-center">
                <input type="text" maxlength="1" class="form-control pin-input mx-1 text-center" autofocus>
                <input type="text" maxlength="1" class="form-control pin-input mx-1 text-center">
                <input type="text" maxlength="1" class="form-control pin-input mx-1 text-center">
                <input type="text" maxlength="1" class="form-control pin-input mx-1 text-center">
            </div>
        <input type="hidden" id="qrcode" name="qrcode">
        </form>
    </div>
</div>
<style>
.pin-input {
    width: 50px;
    height: 50px;
    font-size: 24px;
}
</style>
<script>
    $(document).ready(function() {
        function customAlert(data) {
            if (data.status == "success") {
                Swal.fire({
                    title: "Scan Berhasil",
                    text: data.message,
                    icon: "success",
                    confirmButtonColor: "#6F4E37",
                });
            } else if (data.status == "warning") {
                Swal.fire({
                    title: "Peringatan",
                    text: data.message,
                    icon: "warning",
                    confirmButtonColor: "#6F4E37",
                });
            } else {
                Swal.fire({
                    title: "Gagal",
                    text: data.message,
                    icon: "error",
                    confirmButtonColor: "#6F4E37",
                });
            }
             // Clear all inputs
            $('.pin-input').val('');
            // Refocus on the first input
            $('.pin-input').first().focus();
        }

        $('.pin-input').on('keyup', function(e) {
            if (e.key === 'Backspace' && $(this).val() === '') {
                $(this).prev('.pin-input').focus();
            } else if (e.key >= '0' && e.key <= '9') {
                $(this).next('.pin-input').focus();
            }
            let code = '';
            $('.pin-input').each(function() {
                code += $(this).val();
            });
            $('#qrcode').val(code);

            // Check if all inputs are filled
            if (code.length === 4) {
                var url = "{{ url('scan/in-process') }}";
                 $.ajax({
                    url: url,
                    method: "POST",
                    type: "JSON",
                    data: {
                        _token: "{{ csrf_token() }}",
                        qrcode: code,
                    },
                    success: (res) => {
                        customAlert(res)
                    },
                    error: (err) => {
                        customAlert({status : "error", message: "Scan gagal"})
                    }
                });
            }
        });

        // Prevent pasting more than one character
        $('.pin-input').on('paste', function(e) {
            e.preventDefault();
        });
    })
</script>
@endsection
