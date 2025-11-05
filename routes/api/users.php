<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'index'])->name('index');
Route::post('/', [UserController::class, 'store'])->name('store');
Route::get('/me', [UserController::class, 'me'])->name('me')->middleware('auth:sanctum');
Route::get('/{user}', [UserController::class, 'show'])->name('show');
Route::put('/{user}', [UserController::class, 'update'])->name('update');
Route::patch('/{user}', [UserController::class, 'update'])->name('update');
Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
