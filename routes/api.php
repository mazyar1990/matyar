<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\WikiTitlesController;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/projects/search', [ProjectController::class, 'search']);
});

// Endpoint to search for Arabic text and get Persian equivalents
// Example: GET /api/search/ar?q=مرحبا
Route::get('/search/ar', [WikiSearchController::class, 'searchArabic']);

// Endpoint to search for Persian text and get Arabic equivalents
// Example: GET /api/search/fa?q=سلام
Route::get('/search/fa', [WikiSearchController::class, 'searchPersian']);