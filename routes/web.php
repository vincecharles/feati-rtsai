<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImportExportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/welcome', function () {
    return view('auth.welcome-splash');
})->middleware('auth')->name('welcome.splash');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth','verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Dashboard API routes
    Route::get('/api/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');
    Route::get('/api/dashboard/statistics', [DashboardController::class, 'getStatistics'])->name('dashboard.statistics');

    // Employee routes
    Route::resource('employees', EmployeeController::class);
    Route::get('/api/employees/autocomplete', [EmployeeController::class, 'autocomplete'])->name('employees.autocomplete');
    Route::post('/employees/{employee}/dependents', [EmployeeController::class,'addDependent'])
        ->name('employees.dependents.store');
    Route::delete('/employees/{employee}/dependents/{dependent}', [EmployeeController::class,'removeDependent'])
        ->name('employees.dependents.destroy');

    // Violation routes
    Route::resource('violations', App\Http\Controllers\ViolationController::class);
    Route::post('/violations/{violation}/resolve', [App\Http\Controllers\ViolationController::class, 'resolve'])->name('violations.resolve');
    Route::post('/violations/{violation}/notes', [App\Http\Controllers\ViolationController::class, 'addNote'])->name('violations.notes.store');
    Route::get('/api/violations/statistics', [App\Http\Controllers\ViolationController::class, 'getStatistics'])->name('violations.statistics');
    Route::get('/api/violations/students', [App\Http\Controllers\ViolationController::class, 'getStudents'])->name('violations.students');

    // Student routes
    Route::resource('students', StudentController::class);
    Route::get('/api/students/autocomplete', [StudentController::class, 'autocomplete'])->name('students.autocomplete');
    Route::get('/api/students/statistics', [StudentController::class, 'getStatistics'])->name('students.statistics');
    Route::post('/api/students/bulk-action', [StudentController::class, 'bulkAction'])->name('students.bulk-action');
    Route::post('/api/students/sync-data', [StudentController::class, 'syncStudentData'])->name('students.sync-data');

    // Reports routes
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/students', [ReportsController::class, 'studentEnrollment'])->name('reports.students');
    Route::get('/reports/employees', [ReportsController::class, 'employeeReport'])->name('reports.employees');
    Route::get('/reports/violations', [ReportsController::class, 'violationsReport'])->name('reports.violations');
    // Route::get('/reports/applications', [ReportsController::class, 'applicationReport'])->name('reports.applications');
    // Route::get('/reports/events', [ReportsController::class, 'eventReport'])->name('reports.events');
    Route::get('/reports/analytics', [ReportsController::class, 'analytics'])->name('reports.analytics');

    // Import/Export routes
    Route::get('/import-export', [ImportExportController::class, 'index'])->name('import-export.index');
    Route::post('/import/students', [ImportExportController::class, 'importStudents'])->name('import.students');
    Route::post('/import/users', [ImportExportController::class, 'importUsers'])->name('import.users');
    Route::get('/export/students', [ImportExportController::class, 'exportStudents'])->name('export.students');
    Route::get('/export/users', [ImportExportController::class, 'exportUsers'])->name('export.users');
    Route::get('/export/students/csv', [ImportExportController::class, 'exportStudentsCSV'])->name('export.students.csv');
    Route::get('/export/users/csv', [ImportExportController::class, 'exportUsersCSV'])->name('export.users.csv');
    Route::get('/export/violations', [ImportExportController::class, 'exportViolations'])->name('export.violations');
    Route::get('/export/violations/csv', [ImportExportController::class, 'exportViolationsCSV'])->name('export.violations.csv');
    Route::get('/import/template/{type}', [ImportExportController::class, 'downloadTemplate'])->name('import.template');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/student.php';

// API Authentication Routes
Route::prefix('api/auth')->group(function () {
    // Registration API
    Route::post('/register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'apiStore']);
    Route::get('/check-email', [App\Http\Controllers\Auth\RegisteredUserController::class, 'checkEmail']);
    Route::get('/roles', [App\Http\Controllers\Auth\RegisteredUserController::class, 'getRoles']);
    
    // Login/Logout API
    Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'apiStore']);
    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'apiDestroy']);
    Route::get('/session', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'sessionInfo']);
    
    // Password Reset API
    Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'apiStore']);
    Route::post('/reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'apiStore']);
    Route::post('/validate-token', [App\Http\Controllers\Auth\NewPasswordController::class, 'validateToken']);
    Route::get('/check-reset-email', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'checkEmail']);
    
    // Password Update API
    Route::put('/password', [App\Http\Controllers\Auth\PasswordController::class, 'apiUpdate']);
    Route::post('/check-password-strength', [App\Http\Controllers\Auth\PasswordController::class, 'checkPasswordStrength']);
    Route::get('/password-requirements', [App\Http\Controllers\Auth\PasswordController::class, 'getPasswordRequirements']);
});

