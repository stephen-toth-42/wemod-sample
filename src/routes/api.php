<?php

use Illuminate\Support\Facades\Route;

Route::post('/upload', [App\Http\Controllers\IngestController::class, 'handleUpload']);
Route::get('/'.env("SHORT_LINK_PATH").'/{id}', [App\Http\Controllers\PageController::class, 'handleRequest']);
Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'handleRequest']);
