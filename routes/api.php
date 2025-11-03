<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::prefix('users')->as('users.')->group(base_path('routes/api/users.php'));
});
