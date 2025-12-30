<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\VisualController;
use App\Http\Middleware\EnsureVisitorCookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/admin', [AuthController::class, 'registerAdmin']);
Route::post('/login', [AuthController::class, 'login']);

// Public section routes
Route::get('/sections', [SectionController::class, 'index']);
Route::get('/sections/{id}', [SectionController::class, 'show']);

// Public issue routes
Route::get('/issues', [IssueController::class, 'index']);
Route::get('/issues/{id}', [IssueController::class, 'show']);

// Public article routes
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show'])
    ->middleware(EnsureVisitorCookie::class);

// Public visuals routes
Route::get('/visuals', [VisualController::class, 'index']);
Route::get('/visuals/{visual}', [VisualController::class, 'show']);

// Public Individual Support Routes
Route::prefix('support/individual')->group(function () {
    Route::post('store', [\App\Http\Controllers\API\IndividualSupportRequestController::class, 'store']);
    Route::post('status', [\App\Http\Controllers\API\IndividualSupportRequestController::class, 'checkStatus']);
});

// Public Institutional Support Routes
Route::prefix('support/institutional')->group(function () {
    Route::post('store', [\App\Http\Controllers\API\InstitutionalSupportRequestController::class, 'store']);
    Route::post('status', [\App\Http\Controllers\API\InstitutionalSupportRequestController::class, 'checkStatus']);
});

// Support Settings (Public)
Route::get('support/settings', [\App\Http\Controllers\API\SupportSettingController::class, 'index']);


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Protected issue routes
    Route::post('/issues', [IssueController::class, 'store']);
    Route::put('/issues/{issue}', [IssueController::class, 'update']);
    Route::delete('/issues/{issue}', [IssueController::class, 'destroy']);

    // Protected article routes (Create, Update, Delete)
    // Create article with section in URL
    Route::post('/sections/{section}/articles', [ArticleController::class, 'store']);
    // Or just generic store route
    Route::post('/articles', [ArticleController::class, 'store']);

    // Legacy store route (optional, can keep or remove based on preference, removing to force new structure)
    // Route::post('/articles', [ArticleController::class, 'store']);

    Route::match(['put', 'post'], '/articles/{article}', [ArticleController::class, 'update']);
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy']);

    // Visuals Routes
    Route::post('/visuals', [VisualController::class, 'store']);
    Route::put('/visuals/{visual}', [VisualController::class, 'update']);
    Route::delete('/visuals/{visual}', [VisualController::class, 'destroy']);
    // Route::apiResource('visuals', VisualController::class)->except(['show', 'index']);

    // Backup Routes (Admin only)
    Route::middleware(['admin'])->group(function () {
        Route::get('/backups', [BackupController::class, 'index']);
        Route::get('/backups/history', [BackupController::class, 'history']);
        Route::post('/backups/upload', [BackupController::class, 'upload']);
        Route::get('/backups/download', [BackupController::class, 'download'])->name('backup.download');
        Route::post('/backups/create', [BackupController::class, 'create']);
        Route::post('/backups/restore', [BackupController::class, 'restore']);
        // Route::post('/register', [AuthController::class, 'register']);

        // Route::post('/set-role/{user}', [AuthController::class, 'setRole']);

        // Support Settings (Admin Update)
        Route::post('/support/settings/update', [\App\Http\Controllers\API\SupportSettingController::class, 'update']); 
        
        // Library Management (Admin)
        Route::apiResource('library/series', \App\Http\Controllers\API\BookSeriesController::class);
        Route::apiResource('library/books', \App\Http\Controllers\API\BookController::class);

        // Feedback Management (Admin)
        Route::get('feedback', [\App\Http\Controllers\API\FeedbackController::class, 'index']);
    });
});

// Admin Feedback (Temporarily Public for Testing)
// Route::get('admin/feedback', [\App\Http\Controllers\API\FeedbackController::class, 'index']); // Removed

// Public Library Routes
Route::prefix('library')->group(function() {
    Route::get('books', [\App\Http\Controllers\API\BookController::class, 'index']);
    Route::get('books/{id}', [\App\Http\Controllers\API\BookController::class, 'show']);
    Route::post('books/{id}/rate', [\App\Http\Controllers\API\BookController::class, 'rate']);
});

// Platform Satisfaction Rating (Public)
Route::get('/platform-rating', [\App\Http\Controllers\API\PlatformRatingController::class, 'index']);
Route::post('/platform-rating', [\App\Http\Controllers\API\PlatformRatingController::class, 'store']);

// Feedback (Public)
Route::post('/feedback', [\App\Http\Controllers\API\FeedbackController::class, 'store']);
