@extends('template.template')
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Undangan</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ url('dashboard') }}">Dashboard</a></div>
                    <div class="breadcrumb-item">Invitation</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Data Undangan</h2>

                <div class="card">
                    <div class="card-header">
                        <a class="btn btn-sm btn-primary" href="{{ url('invite/create') }}">
                            <i class="fa fa-plus"></i> Tambah Undangan
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>QrCode</th>
                                        <th>Nama Tamu</th>
                                        <th>Jenis Tamu</th>
                                        <th>No Meja</th>
                                        <th>Created by</th>
                                        <th>Kirim Undangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invitations as $key => $invitation)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $invitation->qrcode_invitation }}</td>
                                            <td>{{ $invitation->name_guest }}</td>
                                            <td>{{ strtoupper($invitation->type_invitation) }}</td>
                                            <td>
                                                {{ $invitation->table_number_invitation }}
                                                {!! $invitation->information_invitation != '' ? '<br>' . $invitation->information_invitation : '' !!}
                                            </td>
                                            <td>{{ ucfirst($invitation->created_by_guest) }}</td>
                                            <td>
                                                @if (mySetting()->send_email == 1)
                                                    @if ($invitation->send_email_invitation != 1)
                                                        <span class="text-warning font-italic">Waiting</span> &nbsp; 
                                                        <a class="btn btn-sm btn-info send-email" title="Kirim Email"
                                                            data-email="{{ $invitation->email_guest }}"
                                                            data-name="{{ $invitation->name_guest }}"
                                                            href="{{ url('invite/send-email?guestQrcode=' . $invitation->qrcode_invitation . '&guestMail=' . $invitation->email_guest . '&guestName=' . $invitation->name_guest) }}">
                                                            <i class="fa fa-envelope"></i> Send Email
                                                        </a>
                                                    @else
                                                        <span class="text-success font-italic">Sent</span> &nbsp; 
                                                        <a class="btn btn-sm btn-info send-email" title="Kirim Ulang Email"
                                                            data-email="{{ $invitation->email_guest }}"
                                                            data-name="{{ $invitation->name_guest }}"
                                                            href="{{ url('invite/send-email?guestQrcode=' . $invitation->qrcode_invitation . '&guestMail=' . $invitation->email_guest . '&guestName=' . $invitation->name_guest) }}">
                                                            <i class="fa fa-envelope"></i> Resend Email
                                                        </a>
                                                    @endif
                                                @endif
                                                @if (mySetting()->send_whatsapp == 1)
                                                    <a class="btn btn-sm btn-info send-whatsapp" title="Kirim Whatsapp" target="_blank"
                                                        href="{{ 'https://api.whatsapp.com/send?phone='.decode_phone($invitation->phone_guest).'&text=Link%20undangan%20:%20'.url('').$invitation->link_invitation }}">
                                                        <i class="fab fa-whatsapp"></i> Send WA
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a target="_blank" data-toggle="tooltip" data-placement="top"
                                                    data-original-title="Link Undangan Publik" class="btn btn-sm btn-secondary"
                                                    href="{{ url('/invitation/' . $invitation->qrcode_invitation) }}">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                                <a data-toggle="tooltip" data-placement="top" data-original-title="Edit"
                                                    class="btn btn-sm btn-primary"
                                                    href="{{ url('invite/edit/' . $invitation->id_invitation) }}"><i
                                                        class="fa fa-pencil-alt"></i></a>
                                                <form action="{{ url('invite/delete') }}" method="POST" class="d-inline"
                                                    id="del-{{ $invitation->id_invitation }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <input type="hidden" name="id_invitation"
                                                        value="{{ $invitation->id_invitation }}">
                                                    <input type="hidden" name="qrcode"
                                                        value="{{ $invitation->qrcode_invitation }}">
                                                    <button data-toggle="tooltip" data-placement="top"
                                                        data-original-title="Hapus" class="btn btn-danger btn-sm"
                                                        data-confirm="Hapus Data|Anda yakin hapus data ini?"
                                                        data-confirm-yes="$('#del-{{ $invitation->id_invitation }}').submit()">
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
        </section>
    </div>

    <div class="modal fade" id="modal-send-email" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Konfirmasi Kirim Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input id="data-email" type="hidden" value="">
                    <input id="data-href" type="hidden" value="">
                    <span>Undangan akan dikirim ke</span><br />
                    <span id="email-guest"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button id="send-email-process" type="button" class="btn btn-dark btn-icon icon-left">
                        <i class="fas fa-paper-plane"></i> Kirim
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loading" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Proses Mengirim Email</h5>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> --}}
                </div>
                <div class="modal-body">
                    <button class="btn btn-warning" type="button" disabled>
                        <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"
                            style="vertical-align:-.35em;;"></span>
                        &nbsp; Loading ...
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            $(".send-email").click(function(e) {
                e.preventDefault()
                let href = $(this).prop("href")
                let name = $(this).data("name")
                let email = $(this).data("email")
                $("#data-email").val(email)
                $("#data-href").val(href)
                $("#email-guest").html("<b>" + name + "</b> - <b>" + email + "</b>")
                $("#modal-send-email").modal('show');
            })
            $("#send-email-process").click(function() {
                $("#modal-send-email").modal('hide');
                $("#loading").modal('show');
                window.location.href = $("#data-href").val()
            })

        })
    </script>
@endsection
