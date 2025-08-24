<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\TranslationExportController;

Route::prefix('api/v1')->group(function () {
    Route::get('/locales', [LocaleController::class, 'index']);
    Route::post('/locales', [LocaleController::class, 'store'])->middleware(['auth.api']);

    Route::get('/translations', [TranslationController::class, 'index']);
    Route::get('/translations/{translation}', [TranslationController::class, 'show']);
    Route::post('/translations', [TranslationController::class, 'store'])->middleware(['auth.api']);
    Route::put('/translations/{translation}', [TranslationController::class, 'update'])->middleware(['auth.api']);
    Route::patch('/translations/{translation}', [TranslationController::class, 'update'])->middleware(['auth.api']);
    Route::delete('/translations/{translation}', [TranslationController::class, 'destroy'])->middleware(['auth.api']);
    Route::get('/translations/search', [TranslationController::class, 'search'])->middleware(['auth.api']);

    Route::get('/translations/export', [TranslationExportController::class, 'export']);
    Route::get('/translations/export-all', [TranslationExportController::class, 'exportAll']);
});
