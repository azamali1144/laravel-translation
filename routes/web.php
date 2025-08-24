<?php

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocaleController;
use App\Http\Controllers\Api\TranslationController;
use App\Http\Controllers\Api\TranslationExportController;

Route::prefix('api/auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    // Protected routes
    Route::middleware('jwt.auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

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

Route::get('/openapi.yaml', function () {
    $path = public_path('openapi.yaml');
    if (!file_exists($path)) abort(404);

    return response()->file($path, [
        'Content-Type' => 'application/x-yaml',
        'Access-Control-Allow-Origin' => '*', // or your frontend origin
    ]);
});

Route::get('/swagger', function () {
    return redirect('/index.html');
});


Route::get('/docs', function () {
    return view('docs'); // if you created resources/views/docs.blade.php
});

