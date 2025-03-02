<html style="font-family: sans-serif; -webkit-text-size-adjust: 100%; -webkit-tap-highlight-color: transparent;">
<head>
</head>
<body style="margin: 0; padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Arial, Noto Sans, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
        font-size: 1rem; font-weight: 400; line-height: 1.5;">
    
    @php
	$bg_extension = explode('.', $event->image_bg_event);
	$img_extension = explode('.', $event->image_event);
	@endphp

    <div style="width:680px;
                @if ($event->image_bg_event != '' && $event->image_bg_status == 1)
                background-image: url('cid:bg');
                background-size: auto;
                @endif
                background-color: {{ $event->color_bg_event ?? "#6c3c0c" }};
                color: {{ $event->color_text_event ?? "#e3eaef" }}">
        <div style="text-align: center; color: {{ $event->color_text_event ?? "#e3eaef" }};">
            <img src="cid:imgtop" style="width:250px; margin-top:1rem" alt="">

            <div style="padding-top:10px; color: {{ $event->color_text_event ?? "#e3eaef" }};">
                {{ $event->type_event }}
            </div>
            <div style="font-size:1.6rem; color: {{ $event->color_text_event ?? "#e3eaef" }};">
                {!! nl2br($event->name_event) !!}
            </div>
            <div style="margin:20px 0 25px 0; color: {{ $event->color_text_event ?? "#e3eaef" }};">
                <i>Kepada Yth.</i>
                <br/>
                <div style="font-size:18px; font-weight:bold; color: {{ $event->color_text_event ?? "#e3eaef" }};">
                    {{ @$invt->name_guest }}
                </div>
            </div>

            <table style="width:100%; color: {{ $event->color_text_event ?? "#e3eaef" }};" border="0">
                <tr>
                    <td style="width:10%; text-align:right; vertical-align:top;">
                        <h4 style="padding-right:10px; margin:0 0 5px 0;">{{ \Carbon\Carbon::parse($event->start_event)->isoFormat('dddd, DD MMMM YYYY') }}</h4>
                        <span style="padding-right:10px; font-size:13px; text-align:right;">
                            {{ \Carbon\Carbon::parse($event->start_event)->isoFormat('hh:mm a') . ' - ' . \Carbon\Carbon::parse($event->end_event)->isoFormat('hh:mm a') }}
                        </span>
                    </td>
                    <td style="width:0.02%; background-color: {{ $event->color_text_event ?? "#e3eaef" }} !important;">
                        {{-- <span style="padding:0 1px; background-color: {{ $event->color_text_event ?? "#e3eaef" }}; height: 100%"></span> --}}
                    </td>
                    <td style="width:10%; padding-bottom:5px; text-align:left;">
                        <h4 style="padding-left:10px; margin:0 0 5px 0;">{{ $event->place_event }}</h4>
                        <span style="padding-left:10px; font-size:13px;">
                            {{ $event->location_event }}
                            <br>
                            <a href="{{ $event->link_maps }}" target="_blank" style="color: {{ $event->color_text_event ?? "#e3eaef" }}; text-decoration: underline; font-size:13px; padding-left:10px;">
                                Link Google Maps
                            </a>
                        </span>
                    </td>
                </tr>
            </table>

            <h3 style="margin: 17px 0 0 0; color: {{ $event->color_text_event ?? "#e3eaef" }};">
                {{ strtoupper(@$invt->type_invitation) }}
                {{ @$invt->table_number_invitation != null ? "- ". ucwords($invt->table_number_invitation) : "" }}
            </h3>
            <div style="color: {{ $event->color_text_event ?? "#e3eaef" }};">
                {{ @$invt->information_invitation }}
            </div>

            <div style="margin-top: 30px">
                <img src="cid:qrcode" class="rounded" style="width: 150px;border-radius: 0.25rem;" alt="...">
                <h4 style="margin: 5px 0 0 0; color: {{ $event->color_text_event ?? "#e3eaef" }};">
                    {{ @$invt->qrcode_invitation }}
                </h4>

                <div style="">
                    <a style="display: inline-block;
                        font-weight: 400;
                        color: #212529 !important;
                        vertical-align: middle;
                        padding: 0.375rem 0.75rem;
                        font-size: 1rem;
                        background-color: #ffa426;
                        text-decoration: none;
                        border-radius: 50rem;
                        margin: 1rem 0;"
                        href="{{ url('/invitation/' . @$invt->qrcode_invitation) }}" target="_blank">Buka Link Undangan</a>
                </div>

                <div style="font-size:12px; color: {{ $event->color_text_event ?? "#e3eaef" }};">
                    <i>* Simpan barcode dan tunjukkan pada saat acara.</i>
                </div>

                <div style="margin-top:25px; color: {{ $event->color_text_event ?? "#e3eaef" }};">
                    {!! nl2br($event->information_event) !!}
                </div>

            </div>

            <div style="padding:20px 0 5px 0; font-size:9px;">
               ~ Developed by YukCoding Media ~
            </div>

        </div>
    </div>

</body>
</html>