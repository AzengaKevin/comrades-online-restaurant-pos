<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'index'])->name('index');
Route::get('/me', [UserController::class, 'me'])->name('me')->middleware('auth:sanctum');
