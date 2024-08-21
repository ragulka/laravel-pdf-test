<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/direct', [PdfController::class, 'direct']);
Route::get('/queued', [PdfController::class, 'queued']);

