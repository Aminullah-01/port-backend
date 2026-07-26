<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\SkillController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Public endpoints
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::get('/settings', [SettingController::class, 'show']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);

    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{blog}', [BlogController::class, 'show']);

    Route::get('/skills', [SkillController::class, 'index']);
    Route::get('/skills/{skill}', [SkillController::class, 'show']);

    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);

    Route::get('/certificates', [CertificateController::class, 'index']);
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show']);

    Route::post('/contact', [ContactController::class, 'send']);

    // Admin protected endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Profile & settings update (supports POST, PUT, and PATCH for FormData method spoofing)
        Route::match(['POST', 'PUT', 'PATCH'], '/profile', [ProfileController::class, 'update']);
        Route::match(['POST', 'PUT', 'PATCH'], '/settings', [SettingController::class, 'update']);
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Resource updates supporting POST, PUT, PATCH
        Route::post('/projects', [ProjectController::class, 'store']);
        Route::match(['POST', 'PUT', 'PATCH'], '/projects/{project}', [ProjectController::class, 'update']);
        Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

        Route::post('/blogs', [BlogController::class, 'store']);
        Route::match(['POST', 'PUT', 'PATCH'], '/blogs/{blog}', [BlogController::class, 'update']);
        Route::delete('/blogs/{blog}', [BlogController::class, 'destroy']);

        Route::post('/skills', [SkillController::class, 'store']);
        Route::match(['POST', 'PUT', 'PATCH'], '/skills/{skill}', [SkillController::class, 'update']);
        Route::delete('/skills/{skill}', [SkillController::class, 'destroy']);

        Route::post('/services', [ServiceController::class, 'store']);
        Route::match(['POST', 'PUT', 'PATCH'], '/services/{service}', [ServiceController::class, 'update']);
        Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

        Route::post('/certificates', [CertificateController::class, 'store']);
        Route::match(['POST', 'PUT', 'PATCH'], '/certificates/{certificate}', [CertificateController::class, 'update']);
        Route::delete('/certificates/{certificate}', [CertificateController::class, 'destroy']);

        Route::get('/contact', [ContactController::class, 'index']);
        Route::get('/contact/{contact}', [ContactController::class, 'show']);
        Route::post('/contact/{contact}/read', [ContactController::class, 'markAsRead']);
        Route::delete('/contact/{contact}', [ContactController::class, 'destroy']);
    });
});
