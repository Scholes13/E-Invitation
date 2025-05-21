<a class="dropdown-item" href="{{ url('invite/view/'.$item->id) }}">
    <i class="fas fa-envelope"></i> Lihat Undangan
</a>

@php
$customQrEnabled = property_exists(mySetting(), 'enable_custom_qr') ? mySetting()->enable_custom_qr == 1 : false;
@endphp

@if($customQrEnabled && count($customQrTemplates) > 0)
<div class="dropdown-divider"></div>
<a class="dropdown-item" href="#" onclick="showCustomQrModal({{ $item->id }})">
    <i class="fas fa-qrcode"></i> Generate Custom QR
</a>
@endif 