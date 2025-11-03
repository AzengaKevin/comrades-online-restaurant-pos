<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/users/me', [UserController::class, 'me'])->name('me')->middleware('auth:sanctum');
