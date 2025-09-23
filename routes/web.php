<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect()->route('dashboard.register');
});

// Serve static assets
Route::get('/assets/{path}', function ($path) {
    $filePath = public_path('assets/' . $path);

    if (file_exists($filePath)) {
        $mimeType = mime_content_type($filePath);
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000'
        ]);
    }

    abort(404);
})->where('path', '.*');
