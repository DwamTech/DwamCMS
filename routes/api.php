<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\LinkController;
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

// Public audios routes
Route::get('/audios', [AudioController::class, 'index']);
Route::get('/audios/{audio}', [AudioController::class, 'show']);

// Public galleries routes
Route::get('/galleries', [GalleryController::class, 'index']);
Route::get('/galleries/{gallery}', [GalleryController::class, 'show']);

// Public links routes
Route::get('/links', [LinkController::class, 'index']);
Route::get('/links/{link}', [LinkController::class, 'show']);

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
    Route::match(['put', 'post'], '/visuals/{visual}', [VisualController::class, 'update']);
    Route::delete('/visuals/{visual}', [VisualController::class, 'destroy']);
    // Route::apiResource('visuals', VisualController::class)->except(['show', 'index']);

    // Audios Routes
    Route::post('/audios', [AudioController::class, 'store']);
    Route::match(['put', 'post'], '/audios/{audio}', [AudioController::class, 'update']);
    Route::delete('/audios/{audio}', [AudioController::class, 'destroy']);

    // Galleries Routes
    Route::post('/galleries', [GalleryController::class, 'store']);
    Route::match(['put', 'post'], '/galleries/{gallery}', [GalleryController::class, 'update']);
    Route::delete('/galleries/{gallery}', [GalleryController::class, 'destroy']);

    // Links Routes
    Route::post('/links', [LinkController::class, 'store']);
    Route::match(['put', 'post'], '/links/{link}', [LinkController::class, 'update']);
    Route::delete('/links/{link}', [LinkController::class, 'destroy']);


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
        Route::post('/admin/support/settings/update', [\App\Http\Controllers\API\SupportSettingController::class, 'update']);
        
        // Individual Support Admin Requests
        Route::get('/admin/support/individual/requests', [\App\Http\Controllers\API\IndividualSupportRequestController::class, 'index']);
        Route::get('/admin/support/individual/requests/{id}', [\App\Http\Controllers\API\IndividualSupportRequestController::class, 'show']);
        Route::post('/admin/support/individual/requests/{id}/update', [\App\Http\Controllers\API\IndividualSupportRequestController::class, 'update']);
        Route::delete('/admin/support/individual/requests/{id}', [\App\Http\Controllers\API\IndividualSupportRequestController::class, 'destroy']);

        // Institutional Support Admin Requests
        Route::get('/admin/support/institutional/requests', [\App\Http\Controllers\API\InstitutionalSupportRequestController::class, 'index']);
        Route::get('/admin/support/institutional/requests/{id}', [\App\Http\Controllers\API\InstitutionalSupportRequestController::class, 'show']);
        Route::post('/admin/support/institutional/requests/{id}/update', [\App\Http\Controllers\API\InstitutionalSupportRequestController::class, 'update']);
        Route::delete('/admin/support/institutional/requests/{id}', [\App\Http\Controllers\API\InstitutionalSupportRequestController::class, 'destroy']);
        
        // Feedback Delete
        Route::delete('admin/feedback/{id}', [\App\Http\Controllers\API\FeedbackController::class, 'destroy']);

        // Library Management (Admin)
        Route::apiResource('admin/library/series', \App\Http\Controllers\API\BookSeriesController::class);
        Route::apiResource('admin/library/books', \App\Http\Controllers\API\BookController::class);

        // Feedback Management (Admin Index)
        Route::get('admin/feedback', [\App\Http\Controllers\API\FeedbackController::class, 'index']);

        // Dashboard & Analytics
        Route::prefix('admin/dashboard')->group(function() {
            Route::get('summary', [\App\Http\Controllers\API\DashboardController::class, 'summary']);
            Route::get('analytics', [\App\Http\Controllers\API\DashboardController::class, 'analytics']);
        });
        
        Route::get('admin/support-requests/recent', [\App\Http\Controllers\API\DashboardController::class, 'recentRequests']);
        Route::get('admin/notifications/unread-count', [\App\Http\Controllers\API\DashboardController::class, 'unreadNotificationsCount']);
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
