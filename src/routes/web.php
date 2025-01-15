<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/'.env("SHORT_LINK_PATH").'/{id}', [App\Http\Controllers\PageController::class, 'handleRequest']);
