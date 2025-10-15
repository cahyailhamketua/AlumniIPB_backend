<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AlumniController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\EventController;

// Endpoint untuk tes API
Route::get('/test', function () {
    return response()->json(['message' => 'API works!']);
});

// Resource endpoint untuk masing-masing controller
Route::apiResource('alumni', AlumniController::class);
Route::apiResource('gallery', GalleryController::class);
Route::apiResource('events', EventController::class);

// Public routes for Articles
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{id}', [ArticleController::class, 'show']);

// Authenticated routes for Articles
Route::middleware('auth:sanctum')->group(function () {
    Route::post('articles/{id}/like', [ArticleController::class, 'like']);
    Route::post('articles/{id}/comment', [ArticleController::class, 'comment']);
    Route::post('articles', [ArticleController::class, 'store']);
    Route::put('articles/{id}', [ArticleController::class, 'update']);
    Route::delete('articles/{id}', [ArticleController::class, 'destroy']);
});

// Endpoint khusus register alumni (opsional, jika ingin terpisah dari resource)
Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AlumniController::class, 'store']);
Route::post('/login', [App\Http\Controllers\Api\AlumniController::class, 'login']);