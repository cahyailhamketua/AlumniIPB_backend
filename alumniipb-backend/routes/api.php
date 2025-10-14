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
Route::apiResource('articles', ArticleController::class);
Route::apiResource('gallery', GalleryController::class);
Route::apiResource('events', EventController::class);

// Endpoint khusus register alumni (opsional, jika ingin terpisah dari resource)
Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AlumniController::class, 'store']);
Route::post('/login', [App\Http\Controllers\Api\AlumniController::class, 'login']);