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
                        <h4>Undangan Tamu</h4>
                        <div class="card-header-action">
                            <a class="btn btn-sm btn-primary" href="{{ url('invite/create') }}"><i class="fa fa-plus"></i> Tambah</a>
                            @php
                            $customQrEnabled = property_exists(mySetting(), 'enable_custom_qr') ? mySetting()->enable_custom_qr == 1 : false;
                            @endphp
                            @if($customQrEnabled)
                            <a class="btn btn-sm btn-warning" href="{{ url('custom-qr/regenerate-all') }}" onclick="return confirm('Ini akan menghasilkan ulang semua kode QR dengan template default. Lanjutkan?')">
                                <i class="fa fa-sync"></i> Regenerate QR Codes
                            </a>
                            @endif
                        </div>
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
                                        <th class="text-center">Kirim Undangan</th>
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
                                            <td class="text-center">
                                                <div class="buttons">
                                                    @if ($invitation->email_bounced)
                                                        <div class="btn-block text-center mb-2">
                                                            <span class="badge badge-danger status-badge"><i class="fa fa-times-circle"></i> Bounced</span>
                                                        </div>
                                                    @elseif ($invitation->email_read)
                                                        <div class="btn-block text-center mb-2">
                                                            <span class="badge badge-info status-badge"><i class="fa fa-check-double"></i> Read</span>
                                                        </div>
                                                    @elseif ($invitation->email_sent)
                                                        <div class="btn-block text-center mb-2">
                                                            <span class="badge badge-success status-badge"><i class="fa fa-check-circle"></i> Sent</span>
                                                        </div>
                                                    @else
                                                        <div class="btn-block text-center mb-2">
                                                            <span class="badge badge-warning status-badge"><i class="fa fa-clock"></i> Waiting</span>
                                                        </div>
                                                    @endif
                                                    
                                                    @if (mySetting()->send_email == 1)
                                                        <a class="btn btn-sm btn-primary mb-2 btn-block" title="Kirim Email"
                                                            data-email="{{ $invitation->email_guest }}"
                                                            data-name="{{ $invitation->name_guest }}"
                                                            href="{{ url('invite/send-email?guestQrcode=' . $invitation->qrcode_invitation . '&guestMail=' . $invitation->email_guest . '&guestName=' . $invitation->name_guest) }}">
                                                            <i class="fa fa-envelope"></i> {{ $invitation->email_sent ? 'Resend Email' : 'Send Email' }}
                                                        </a>
                                                    @endif
                                                    
                                                    @if (mySetting()->send_whatsapp == 1)
                                                        <a class="btn btn-sm btn-success btn-block" title="Kirim Whatsapp" target="_blank"
                                                            href="{{ 'https://api.whatsapp.com/send?phone='.decode_phone($invitation->phone_guest).'&text=Link%20undangan%20:%20'.url('').$invitation->link_invitation }}">
                                                            <i class="fab fa-whatsapp"></i> Send WA
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a target="_blank" data-toggle="tooltip" data-placement="top"
                                                        data-original-title="Link Undangan Publik" class="btn btn-sm btn-secondary mb-2"
                                                        href="{{ url('/invitation/' . $invitation->qrcode_invitation) }}">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </div>
                                                <div class="btn-group">
                                                    <a data-toggle="tooltip" data-placement="top" data-original-title="Edit"
                                                        class="btn btn-sm btn-primary mb-2"
                                                        href="{{ url('invite/edit/' . $invitation->id_invitation) }}">
                                                        <i class="fa fa-pencil-alt"></i>
                                                    </a>
                                                </div>
                                                <div class="btn-group">
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
                                                </div>
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

    <!-- Modal untuk Custom QR -->
    <div class="modal fade" id="customQrModal" tabindex="-1" role="dialog" aria-labelledby="customQrModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customQrModalLabel">Generate Custom QR Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="customQrForm" method="POST" action="">
                        @csrf
                        <div class="form-group">
                            <label for="template_id">Select Template</label>
                            <select class="form-control" id="template_id" name="template_id" required>
                                @foreach($customQrTemplates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="generateCustomQr">Generate</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global function for custom QR modal
        function showCustomQrModal(invitationId) {
            $('#customQrForm').attr('action', '{{ url("custom-qr/generate") }}/' + invitationId + '/' + $('#template_id').val());
            $('#customQrModal').modal('show');
        }
        
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
            
            $('#generateCustomQr').click(function() {
                $('#customQrForm').submit();
            });
            
            $('#template_id').change(function() {
                const invitationId = $('#customQrForm').attr('action').split('/')[4];
                $('#customQrForm').attr('action', '{{ url("custom-qr/generate") }}/' + invitationId + '/' + $(this).val());
            });
        })
    </script>

    <style>
        .table td {
            vertical-align: middle;
        }
        .buttons {
            display: flex;
            flex-direction: column;
        }
        .btn-group {
            margin-bottom: 5px;
            display: block;
        }
        .badge {
            font-size: 85%;
            font-weight: 600;
            display: inline-block;
        }
        .status-badge {
            width: 100%;
            display: block;
            padding: 10px;
            text-align: center;
            border-radius: 3px;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .status-badge i {
            margin-right: 5px;
            font-size: 16px;
        }
        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        .badge-info {
            color: #fff;
            background-color: #17a2b8;
        }
        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }
        .btn-block {
            width: 100%;
            display: block;
        }
    </style>
@endsection
