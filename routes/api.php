<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API routes without CSRF protection
Route::post('/video-info', [DownloadController::class, 'getVideoInfo']);
Route::post('/download', [DownloadController::class, 'store']);
Route::get('/stats', [DownloadController::class, 'getStats'])->name('stats');
Route::get('/welcome', [DownloadController::class, 'welcome']);

