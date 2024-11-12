<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/file/{folder}/{file}', function ($folder, $file) {
    $file = Storage::disk('r2')->exists($folder . '/' . $file);

    dd($file);

    if(!$file) {
        return response()->json([
            'message' => 'File not found',
            'success' => false
        ], 404);
    }

    return response()->make($file, 200, [
        'Content-Type' => Storage::disk('r2')->mimeType($folder . '/' . $file),
        'Content-Disposition' => 'inline; filename="' . $file . '"'
    ]);
});