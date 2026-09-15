<?php

use Illuminate\Support\Facades\Route;

// Serve the SPA for all routes — Vue Router handles navigation on the client.
Route::get('/{any?}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');
