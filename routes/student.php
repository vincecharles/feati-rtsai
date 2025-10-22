<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\ViolationController;
use App\Http\Controllers\Student\ApplicationController;
// use App\Http\Controllers\Student\EventController;
use App\Http\Controllers\Student\ReportController;
use App\Http\Controllers\Student\ProfileController;

// Student routes - require authentication and student role
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('student.dashboard');
    })->name('dashboard');

    // Violations
    Route::resource('violations', ViolationController::class)->only(['index', 'show']);
    Route::get('/violations/{violation}/appeal', [ViolationController::class, 'createAppeal'])->name('violations.appeal');
    Route::post('/violations/{violation}/appeal', [ViolationController::class, 'storeAppeal'])->name('violations.appeal.store');
    Route::get('/violations/statistics', [ViolationController::class, 'getStatistics'])->name('violations.statistics');
    Route::get('/violations/evidence/{evidence}/download', [ViolationController::class, 'downloadEvidence'])->name('violations.evidence.download');

    // Applications
    // Route::resource('applications', ApplicationController::class);
    // Route::get('/applications/statistics', [ApplicationController::class, 'getStatistics'])->name('applications.statistics');

    // Events - Commented out as features were removed
    // Route::get('/events', [EventController::class, 'index'])->name('events.index');
    // Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    // Route::post('/events/{event}/register', [EventController::class, 'register'])->name('events.register');
    // Route::post('/events/{event}/cancel-registration', [EventController::class, 'cancelRegistration'])->name('events.cancel-registration');
    // Route::get('/events/statistics', [EventController::class, 'getStatistics'])->name('events.statistics');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/violations', [ReportController::class, 'violations'])->name('reports.violations');
    // Route::get('/reports/applications', [ReportController::class, 'applications'])->name('reports.applications');
    // Route::get('/reports/events', [ReportController::class, 'events'])->name('reports.events');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Student API routes
Route::middleware(['auth', 'role:student'])->prefix('api/student')->name('api.student.')->group(function () {
    
    // Violations API
    Route::get('/violations', [ViolationController::class, 'index']);
    Route::get('/violations/{violation}', [ViolationController::class, 'show']);
    Route::get('/violations/statistics', [ViolationController::class, 'getStatistics']);
    // Route::get('/violations/types', [ViolationController::class, 'getViolationTypes']);
    // Route::post('/violations/{violation}/appeal', [ViolationController::class, 'storeAppeal']);

    // Applications API - Commented out
    // Route::get('/applications', [ApplicationController::class, 'index']);
    // Route::post('/applications', [ApplicationController::class, 'store']);
    // Route::get('/applications/{application}', [ApplicationController::class, 'show']);
    // Route::patch('/applications/{application}', [ApplicationController::class, 'update']);
    // Route::delete('/applications/{application}', [ApplicationController::class, 'destroy']);
    // Route::get('/applications/statistics', [ApplicationController::class, 'getStatistics']);

    // Events API - Commented out
    // Route::get('/events', [EventController::class, 'index']);
    // Route::get('/events/{event}', [EventController::class, 'show']);
    // Route::post('/events/{event}/register', [EventController::class, 'register']);
    // Route::post('/events/{event}/cancel-registration', [EventController::class, 'cancelRegistration']);
    // Route::get('/events/statistics', [EventController::class, 'getStatistics']);

    // Reports API
    Route::get('/reports/violations', [ReportController::class, 'violations']);
    // Route::get('/reports/applications', [ReportController::class, 'applications']);
    // Route::get('/reports/events', [ReportController::class, 'events']);
});
