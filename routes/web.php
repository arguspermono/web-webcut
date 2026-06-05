<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\MediaEditController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Upload and processing status
Route::post('/project/upload', [MediaUploadController::class, 'store'])->name('project.upload');
Route::get('/project/{media}/status', [MediaUploadController::class, 'status'])->name('project.status');

// Editor routes
Route::get('/project/{media}/edit', [MediaEditController::class, 'edit'])->name('project.edit');
Route::post('/project/edit', [MediaEditController::class, 'store'])->name('project.edit.store');
Route::get('/project/edit/{mediaEdit}/status', [MediaEditController::class, 'show'])->name('project.edit.status');
Route::delete('/project/{media}', [MediaEditController::class, 'destroy'])->name('project.destroy');

// Stream Watch route
Route::get('/project/{media}/watch', [StreamController::class, 'watch'])->name('project.watch');
Route::get('/stream/{media}', [StreamController::class, 'show'])->name('media.stream');
