@extends('template.template')
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>QR Code Creator</h1>
        </div>
        
        <div class="row">
            <div class="col-12 mb-4">
                <div class="hero bg-primary text-white">
                    <div class="hero-inner">
                        <h2>Create Beautiful QR Codes</h2>
                        <p class="lead">Generate custom QR codes with perfect logo placement and extensive styling options.</p>
                        <div class="mt-4">
                            <a href="{{ route('custom-qr.create') }}" class="btn btn-outline-white btn-lg btn-icon icon-left">
                                <i class="fas fa-qrcode"></i> Create New QR Code
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>My QR Templates</h4>
                        <div class="card-header-action">
                            <a href="{{ route('custom-qr.create') }}" class="btn btn-primary">Create New QR Code</a>
                            <a href="{{ route('custom-qr.regenerate-all') }}" class="btn btn-success ml-2" onclick="return confirm('This will regenerate all invitation QR codes using the default template. Continue?')">
                                <i class="fas fa-sync-alt"></i> Regenerate All QR Codes
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible show fade">
                                <div class="alert-body">
                                    <button class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                    {{ session('success') }}
                                </div>
                            </div>
                        @endif
                        
                        <div class="table-responsive">
                            <table class="table table-striped" id="custom-qr-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Foreground Color</th>
                                        <th>Background Color</th>
                                        <th>Logo</th>
                                        <th>Preview</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($templates as $template)
                                        <tr>
                                            <td>
                                                {{ $template->name }}
                                                @if($template->is_default)
                                                    <span class="badge badge-success">Default</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $fgColor = json_decode($template->fg_color, true);
                                                    $hexColor = isset($fgColor['r']) ? sprintf('#%02x%02x%02x', $fgColor['r'], $fgColor['g'], $fgColor['b']) : '#000000';
                                                @endphp
                                                <div style="width: 25px; height: 25px; background-color: {{ $hexColor }}; border: 1px solid #ddd;"></div>
                                            </td>
                                            <td>
                                                @php
                                                    $bgColor = json_decode($template->bg_color, true);
                                                    $hexColor = isset($bgColor['r']) ? sprintf('#%02x%02x%02x', $bgColor['r'], $bgColor['g'], $bgColor['b']) : '#ffffff';
                                                @endphp
                                                <div style="width: 25px; height: 25px; background-color: {{ $hexColor }}; border: 1px solid #ddd;"></div>
                                            </td>
                                            <td>
                                                @if ($template->logo_path)
                                                    @php
                                                        $logoPath = str_replace('public/', '', $template->logo_path);
                                                        // Ensure the path has the correct format
                                                        if (strpos($logoPath, '/') !== 0) {
                                                            $logoPath = '/' . $logoPath;
                                                        }
                                                    @endphp
                                                    <img src="{{ Storage::url($logoPath) }}" alt="Logo" width="40" style="max-height: 40px; object-fit: contain;">
                                                @else
                                                    <span class="text-muted">No logo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('custom-qr.preview', $template->id) }}" target="_blank" class="btn btn-icon btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="buttons">
                                                    <a href="{{ route('custom-qr.edit', $template->id) }}" class="btn btn-icon btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('custom-qr.set-default', $template->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-icon btn-sm btn-success" title="Set as Default Template">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('custom-qr.destroy', $template->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-icon btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this template?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No templates found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Only initialize DataTable if there are rows in the table
        // The 'No templates found' row doesn't count
        if ($('#custom-qr-table tbody tr').length > 0 && 
            !$('#custom-qr-table tbody tr td').text().includes('No templates found')) {
            $('#custom-qr-table').DataTable();
        }
    });
</script>
@endpush
@endsection 