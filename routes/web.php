<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\MediaEditController;

Route::get('/', function () {
    return view('welcome');
});

// Monolithic Routes
Route::post('/media/upload', [MediaUploadController::class, 'store'])->name('media.upload');
Route::get('/media/{media}/status', [MediaUploadController::class, 'status'])->name('media.status');
Route::get('/stream/{media}', [StreamController::class, 'show'])->name('media.stream');
Route::post('/media/edit', [MediaEditController::class, 'store'])->name('media.edit');
Route::get('/media/edit/{mediaEdit}', [MediaEditController::class, 'show'])->name('media.edit.status');
