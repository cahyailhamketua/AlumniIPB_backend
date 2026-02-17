<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AlumniController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\JobOpeningController;
use App\Http\Controllers\Api\OrganizationalStructureController;

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
    Route::post('/change-password', [AlumniController::class, 'changePassword']);
    // Alias path so client hitting /api/alumni/change-password doesn't get 405
    Route::post('/alumni/change-password', [AlumniController::class, 'changePassword']);
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

// Organizational Structures routes
Route::get('organizational-structures', [OrganizationalStructureController::class, 'index']);
Route::get('organizational-structures/{id}', [OrganizationalStructureController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('organizational-structures', [OrganizationalStructureController::class, 'store']);
    Route::put('organizational-structures/{id}', [OrganizationalStructureController::class, 'update']);
    Route::delete('organizational-structures/{id}', [OrganizationalStructureController::class, 'destroy']);
});

// Job Openings routes
Route::get('job-openings', [JobOpeningController::class, 'index']);
// Dropdown helpers: distinct industries and positions
Route::get('job-openings/industries', [JobOpeningController::class, 'industries']);
Route::get('job-openings/positions', [JobOpeningController::class, 'positions']);
Route::get('job-openings/{id}', [JobOpeningController::class, 'show']);

// Authenticated users (alumni and admin) can create; controller handles approval logic
Route::middleware('auth:sanctum')->group(function () {
    Route::post('job-openings', [JobOpeningController::class, 'store']);
    Route::put('job-openings/{id}', [JobOpeningController::class, 'update']);
});

// Admin-only actions
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('job-openings/{id}/approve', [JobOpeningController::class, 'approve']);
    Route::post('job-openings/{id}/deactivate', [JobOpeningController::class, 'deactivate']);
    Route::delete('job-openings/{id}', [JobOpeningController::class, 'destroy']);
});