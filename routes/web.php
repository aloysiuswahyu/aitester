<?php

use App\Http\Controllers\AiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/analisis-teks', [AiController::class, 'index']);
Route::post('/generate', [AiController::class, 'generate'])->name('ai.action');
Route::get('/lms-teks', [AiController::class, 'lms']);
Route::post('/generate-lms', [AiController::class, 'generatelms'])->name('ai.actionlms');
