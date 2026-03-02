<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
Route::post('/login', [AccountController::class, 'login']);
Route::post('/register', [AccountController::class, 'store']);
Route::post('/verify', [AccountController::class, 'verifyEmail']);
Route::post('/forgot-password', [AccountController::class, 'forgotPassword']);
Route::post('/reset-password', [AccountController::class, 'resetPassword']);

// Public review routes
Route::get('/products/{product}/reviews', [ReviewController::class, 'index']);
Route::get('/reviews/{review}', [ReviewController::class, 'show']);

// Routes that require authentication
Route::middleware('auth:sanctum')->group(function () {

    // Route only accessible to authenticated users
    Route::get('/me', function(Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AccountController::class, 'logout']);

    // Email verification routes
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return response()->json(['message' => 'Email verified successfully']);
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent']);
    })->middleware(['throttle:6,1'])->name('verification.send');

    // Example: only verified users can access this route
    Route::middleware('verified')->get('/dashboard', function() {
        return response()->json(['message' => 'Welcome verified user']);
    });

    // Authenticated review routes
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::patch('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
});