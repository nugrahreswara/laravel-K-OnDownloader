<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\SettingsController;

// Home page
Route::get('/', function () {
    return redirect()->route('downloads.create');
});

// Download routes
Route::prefix('downloads')->name('downloads.')->group(function () {
    Route::get('/', [DownloadController::class, 'index'])->name('index');
    Route::get('/create', [DownloadController::class, 'create'])->name('create');
    Route::post('/', [DownloadController::class, 'store'])->name('store');
    Route::get('/{download}', [DownloadController::class, 'show'])->name('show');
    Route::get('/{download}/status', [DownloadController::class, 'status'])->name('status');
    Route::get('/{download}/download', [DownloadController::class, 'download'])->name('download');
    Route::get('/{download}/stream', [DownloadController::class, 'stream'])->name('stream');
    Route::delete('/{download}', [DownloadController::class, 'destroy'])->name('destroy');

    // Bulk operations
    Route::delete('/bulk-delete', [DownloadController::class, 'bulkDestroy'])->name('bulk-destroy');
    Route::patch('/bulk-update-folder', [DownloadController::class, 'bulkUpdateFolder'])->name('bulk-update-folder');

    // API routes for AJAX
    Route::post('/video-info', [DownloadController::class, 'getVideoInfo'])->name('video-info');
    Route::post('/formats', [DownloadController::class, 'getAvailableFormats'])->name('formats');
    Route::post('/direct-download', [DownloadController::class, 'directDownload'])->name('direct-download');
});

// Platform routes
Route::prefix('platforms')->name('platforms.')->group(function () {
    Route::get('/', [PlatformController::class, 'index'])->name('index');
    Route::get('/{platform}', [PlatformController::class, 'show'])->name('show');
    Route::patch('/{platform}/toggle', [PlatformController::class, 'toggle'])->name('toggle');
    Route::get('/{platform}/settings', [PlatformController::class, 'getSettings'])->name('settings');
});

// Settings routes
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::get('/{platform}', [SettingsController::class, 'show'])->name('show');
    Route::put('/{platform}', [SettingsController::class, 'update'])->name('update');
    Route::post('/{platform}/reset', [SettingsController::class, 'reset'])->name('reset');
    Route::get('/defaults', [SettingsController::class, 'getDefaultSettings'])->name('defaults');
});

// Thumbnail proxy route
Route::get('/thumbnail-proxy/{encoded_url}', [DownloadController::class, 'thumbnailProxy'])->name('thumbnail-proxy');

// Static pages
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/donasi', function () {
    return view('donasi');
})->name('donasi');

