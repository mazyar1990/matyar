<?php

use App\Http\Controllers\Api\ProjectController;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/projects/search', [ProjectController::class, 'search']);
});