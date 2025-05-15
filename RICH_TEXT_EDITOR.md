# Rich Text Editor Implementation Guide

This guide explains how to implement a feature-rich WYSIWYG (What You See Is What You Get) text editor in your web applications using Summernote.

## What is Summernote?

Summernote is a JavaScript library that helps you create a WYSIWYG editor similar to what you'd find in Gmail, Outlook, or other email clients. It allows users to format text, insert images, create tables, and more without needing to know HTML.

## Features

- Text formatting (bold, italic, underline, etc.)
- Font styling (size, family, color)
- Paragraph formatting (alignment, lists, indentation)
- Insert images, links, tables, and videos
- HTML code view
- Variable/placeholder insertion
- Real-time preview
- Mobile responsive

## Prerequisites

- jQuery (Summernote depends on jQuery)
- Bootstrap 4 (for the Bootstrap version of Summernote)
- Modern web browser

## Installation

### Method 1: Using CDN (Easiest)

Add these links to your HTML file:

```html
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
```

### Method 2: Using NPM

```bash
npm install summernote
```

Then import it in your JavaScript:

```javascript
import 'summernote/dist/summernote-bs4.css';
import 'summernote/dist/summernote-bs4.js';
```

### Method 3: In Laravel

1. Add the CDN links in your layout file (e.g., app.blade.php)
2. Or use Laravel Mix to bundle the dependencies:

```javascript
// webpack.mix.js
mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .copy('node_modules/summernote/dist/summernote-bs4.min.js', 'public/js')
   .copy('node_modules/summernote/dist/summernote-bs4.min.css', 'public/css');
```

## Basic Implementation

### HTML Setup

```html
<div class="form-group">
    <label>Email Template Editor</label>
    <textarea id="summernote" name="content"></textarea>
</div>
```

### JavaScript Initialization

```javascript
$(document).ready(function() {
    $('#summernote').summernote({
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});
```

## Adding Custom Variable Insertion

If you need to add custom variables or placeholders (like {name}, {email}, etc.):

### HTML for Variable Buttons

```html
<div class="mb-3">
    <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{name}')">Insert Name</button>
    <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{email}')">Insert Email</button>
    <!-- Add more variables as needed -->
</div>
<textarea id="summernote" name="content"></textarea>
```

### JavaScript for Variable Insertion

```javascript
function insertVariable(variable) {
    $('#summernote').summernote('insertText', variable);
}
```

## Real-time Preview

To show a preview of the content as the user types:

### HTML

```html
<div class="form-group">
    <label>Preview</label>
    <div id="preview" class="border p-3" style="min-height: 200px; background-color: white;"></div>
</div>
```

### JavaScript

```javascript
$('#summernote').summernote({
    // other options...
    callbacks: {
        onChange: function(contents) {
            $('#preview').html(contents);
        }
    }
});
```

## Processing Variables in Back-end

When processing the content on the server side, you'll need to replace the variables with actual data:

### PHP Example

```php
/**
 * Replace template variables with actual values
 */
private function replaceTemplateVariables($content, $data)
{
    $replacements = [
        '{name}' => $data->name,
        '{email}' => $data->email,
        '{id}' => $data->id,
        // Add more replacements as needed
    ];
    
    return str_replace(array_keys($replacements), array_values($replacements), $content);
}
```

## Complete Laravel Example

### Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function edit()
    {
        $template = Template::first();
        return view('template.edit', compact('template'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string'
        ]);
        
        $template = Template::first();
        $template->content = $request->content;
        $template->save();
        
        return redirect()->back()->with('success', 'Template updated successfully');
    }
    
    public function preview($id)
    {
        $template = Template::findOrFail($id);
        $content = $this->replaceTemplateVariables($template->content, [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'id' => 123
        ]);
        
        return view('template.preview', compact('content'));
    }
    
    private function replaceTemplateVariables($content, $data)
    {
        $replacements = [
            '{name}' => $data['name'],
            '{email}' => $data['email'],
            '{id}' => $data['id']
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }
}
```

### View (template.edit.blade.php)

```php
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Template</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <form action="{{ route('template.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label>Available Variables</label>
                            <div class="mb-3">
                                <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{name}')">Insert Name</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{email}')">Insert Email</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="insertVariable('{id}')">Insert ID</button>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Template Editor</label>
                            <textarea id="summernote" name="content">{{ $template->content }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Preview</label>
                            <div id="preview" class="border p-3" style="min-height: 200px; background-color: white;"></div>
                        </div>
                        
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'italic', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function(contents) {
                    $('#preview').html(contents);
                }
            }
        });
        
        // Initial preview
        setTimeout(function() {
            $('#preview').html($('#summernote').summernote('code'));
        }, 500);
    });
    
    function insertVariable(variable) {
        $('#summernote').summernote('insertText', variable);
    }
</script>
@endpush
```

## Advanced Configuration Options

### Custom Toolbar

You can customize the toolbar buttons:

```javascript
$('#summernote').summernote({
    toolbar: [
        // Customize which buttons appear in the toolbar
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']]
    ]
});
```

### Placeholder Text

```javascript
$('#summernote').summernote({
    placeholder: 'Write your content here...',
    // other options...
});
```

### Limiting Features

To create a simpler editor with fewer options:

```javascript
$('#summernote').summernote({
    toolbar: [
        ['style', ['bold', 'italic', 'underline']],
        ['para', ['ul', 'ol']],
        ['insert', ['link']]
    ]
});
```

## Troubleshooting

### Editor Not Showing

- Make sure jQuery is loaded before Summernote
- Check browser console for any JavaScript errors
- Verify that all CSS and JS files are properly loaded

### Images Not Loading

- Check if your server allows file uploads
- Verify path permissions
- Consider using a storage service for images

### Editor Too Small/Large

Adjust the height and width parameters:

```javascript
$('#summernote').summernote({
    height: 400,  // set editor height
    width: '100%', // set editor width
    // other options...
});
```

## Resources

- [Summernote Official Documentation](https://summernote.org/getting-started/)
- [GitHub Repository](https://github.com/summernote/summernote)
- [Summernote Examples](https://summernote.org/examples/)

## License

Summernote is released under the MIT License. 