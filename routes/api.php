<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SecurityMobileController;

/*
|--------------------------------------------------------------------------
| API Routes for Security Mobile Application
|--------------------------------------------------------------------------
|
| These routes are for the security personnel mobile application.
| They handle QR/barcode scanning, violation reporting, and student lookup.
|
*/

// Public routes (no authentication required)
Route::prefix('mobile/security')->group(function () {
    // Authentication
    Route::post('/login', [SecurityMobileController::class, 'login'])->name('api.mobile.security.login');
});

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->prefix('mobile/security')->group(function () {
    // Authentication
    Route::post('/logout', [SecurityMobileController::class, 'logout'])->name('api.mobile.security.logout');
    
    // Dashboard
    Route::get('/dashboard/stats', [SecurityMobileController::class, 'getDashboardStats'])
        ->name('api.mobile.security.dashboard.stats');
    
    // QR Code Scanning
    Route::post('/scan', [SecurityMobileController::class, 'scanStudent'])
        ->name('api.mobile.security.scan');
    
    // Student Search
    Route::get('/students/search', [SecurityMobileController::class, 'searchStudents'])
        ->name('api.mobile.security.students.search');
    
    // Violations
    Route::get('/violation-types', [SecurityMobileController::class, 'getViolationTypes'])
        ->name('api.mobile.security.violation-types');
    Route::post('/violations', [SecurityMobileController::class, 'createViolation'])
        ->name('api.mobile.security.violations.create');
    Route::get('/violations', [SecurityMobileController::class, 'getMyViolations'])
        ->name('api.mobile.security.violations.index');
    Route::get('/violations/{id}', [SecurityMobileController::class, 'getViolation'])
        ->name('api.mobile.security.violations.show');
});

// Health check endpoint
Route::get('/mobile/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'FEATI RTSAI Mobile - Security',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String()
    ]);
});
