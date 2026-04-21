<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Common\DocController;

Route::get('/docs', [DocController::class, 'index']);
Route::get('/docs/health', [DocController::class, 'health']);
Route::get('/docs/{endpoint}', [DocController::class, 'docs']);

Route::get('/{path?}', function () {
    return view('welcome');
})->where('path', '.*');