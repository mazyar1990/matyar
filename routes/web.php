<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SourceFileController;
use App\Http\Controllers\TargetUnitController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DashboardController;


use App\Models\TargetUnit;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'display'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/source/upload', [SourceFileController::class, 'store'])->middleware(['auth', 'verified'])->name('source.upload');  

//TODO: add a middleware so that each user just can view his own files not the others, also the fileid should not be shown in the url directly
Route::get('/translate/sfile/{fileId}', [SourceFileController::class, 'show'])->middleware(['auth', 'verified'])->name('source.show'); 

Route::post('/tunit/store', [TargetUnitController::class, 'store'])->middleware(['auth', 'verified'])->name('source.store');  

Route::post('/convert-to-doc', [DocumentController::class, 'convertToDoc']);


require __DIR__.'/auth.php';