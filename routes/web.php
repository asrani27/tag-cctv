<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PublicSurveyController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\SurveyPhotoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Visitor Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/map', [MapController::class, 'index'])->name('map');
Route::get('/map/geojson', [MapController::class, 'geojson'])->name('map.geojson');
Route::get('/survey', [PublicSurveyController::class, 'index'])->name('public.surveys.index');
Route::get('/survey/{id}', [PublicSurveyController::class, 'show'])->name('public.surveys.show');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Survey Photos Chunk Upload & Management
    Route::get('surveys/photos/chunk-status', [SurveyPhotoController::class, 'chunkStatus'])->name('surveys.photos.chunk-status');
    Route::post('surveys/photos/chunk', [SurveyPhotoController::class, 'uploadChunk'])->name('surveys.photos.chunk');
    Route::post('surveys/photos/complete', [SurveyPhotoController::class, 'completeUpload'])->name('surveys.photos.complete');
    Route::delete('surveys/photos/chunk/{uploadId}', [SurveyPhotoController::class, 'cancelUpload'])->name('surveys.photos.cancel');
    Route::delete('surveys/{survey}/photos/{photo}', [SurveyPhotoController::class, 'destroy'])->name('surveys.photos.destroy');

    // Superadmin-only: Export Excel (defined before resource to avoid {survey} catch)
    Route::middleware('superadmin')
        ->get('surveys/export', [SurveyController::class, 'export'])
        ->name('surveys.export');

    Route::resource('surveys', SurveyController::class);

    // Superadmin-only routes
    Route::middleware('superadmin')->group(function () {
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::resource('users', UserController::class)->except(['show']);
    });
});



