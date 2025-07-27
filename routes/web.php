<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

Route::get('/', [FileController::class, 'guide']);
Route::post('/', [FileController::class, 'upload']);
Route::get('/file/{hash}', [FileController::class, 'download']);
Route::get('/delete/{hash}', [FileController::class, 'delete']);
