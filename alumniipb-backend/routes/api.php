<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AlumniController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\TimelineController;

// Public routes for Galleries
Route::get('gallery/categories', [GalleryController::class, 'getAllCategories']);
Route::get('gallery/years', [GalleryController::class, 'getAllYears']);
Route::get('gallery/category/{kategori}', [GalleryController::class, 'getGalleriesByCategory']);
Route::get('gallery/year/{year}', [GalleryController::class, 'getGalleriesByYear']);
Route::get('gallery', [GalleryController::class, 'index']);
Route::get('gallery/{id}', [GalleryController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:alumni'])->group(function () {
    Route::post('gallery/{id}/like', [GalleryController::class, 'like']);
    Route::post('gallery/{id}/comment', [GalleryController::class, 'comment']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('gallery', [GalleryController::class, 'store']);
    Route::put('gallery/{id}', [GalleryController::class, 'update']); // Reverted to original
    Route::delete('gallery/{id}', [GalleryController::class, 'destroy']);
});

// Public routes for Articles
Route::get('articles/categories', [ArticleController::class, 'getAllCategories']);
Route::get('articles/category/{kategori}', [ArticleController::class, 'getArticlesByCategory']);
Route::get('articles/search', [ArticleController::class, 'searchArticles']);
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{id}', [ArticleController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:alumni'])->group(function () {
    Route::post('articles/{id}/like', [ArticleController::class, 'like']);
    Route::post('articles/{id}/comment', [ArticleController::class, 'comment']);
    Route::post('comments/{commentId}/like', [ArticleController::class, 'likeComment']);
    Route::post('articles/{articleId}/comments/{parentId}/reply', [ArticleController::class, 'replyToComment']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('articles', [ArticleController::class, 'store']);
    Route::put('articles/{id}', [ArticleController::class, 'update']);
    Route::delete('articles/{id}', [ArticleController::class, 'destroy']);
});

// Endpoint khusus register alumni 
Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
    return $request->user();
});
Route::post('/register', [AlumniController::class, 'store']);
Route::post('/login', [AlumniController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AlumniController::class, 'logout']);
});

// menampilkan alumni
Route::get('/alumni', [AlumniController::class, 'index']);
Route::get('/alumni/search', [AlumniController::class, 'search']); 
Route::get('/alumni/{id}', [AlumniController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/alumni/{id}', [AlumniController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::delete('/alumni/{id}', [AlumniController::class, 'destroy']);
});

// About Us routes
Route::get('about-us', [AboutUsController::class, 'index']);
Route::get('about-us/show', [AboutUsController::class, 'show']);
Route::get('about-us/organizational-structures', [AboutUsController::class, 'getOrganizationalStructures']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('about-us', [AboutUsController::class, 'store']);
    Route::put('about-us/update', [AboutUsController::class, 'update']);
    Route::delete('about-us', [AboutUsController::class, 'destroy']);

    Route::post('about-us/organizational-structures', [AboutUsController::class, 'addOrganizationalStructure']);
    Route::put('about-us/organizational-structures/{id}', [AboutUsController::class, 'updateOrganizationalStructure']);
    Route::delete('about-us/organizational-structures/{id}', [AboutUsController::class, 'deleteOrganizationalStructure']);
});

// Public routes for Timelines
Route::get('/timelines', [TimelineController::class, 'index']);
Route::get('/timelines/{id}', [TimelineController::class, 'show']);

// Admin routes for Timelines
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/timelines', [TimelineController::class, 'store']);
    Route::put('/timelines/{id}', [TimelineController::class, 'update']);
    Route::delete('/timelines/{id}', [TimelineController::class, 'destroy']);
});