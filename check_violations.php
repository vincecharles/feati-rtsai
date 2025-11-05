<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VIOLATION SYSTEM CHECK ===\n\n";

// Check students
$studentCount = \App\Models\User::whereNotNull('student_id')->count();
echo "✓ Students in database: {$studentCount}\n";

// Check admin
$adminCount = \App\Models\User::whereHas('role', function($q) {
    $q->where('name', 'admin');
})->count();
echo "✓ Admin users: {$adminCount}\n";

// Check violations
$violationCount = \App\Models\Violation::count();
echo "✓ Violations in database: {$violationCount}\n\n";

if ($studentCount === 0) {
    echo "⚠️  WARNING: No students found! You need to create students first.\n";
    echo "   Go to Students menu → New Student to add students.\n\n";
}

if ($adminCount === 0) {
    echo "⚠️  WARNING: No admin user found! Run: php artisan db:seed\n\n";
} else {
    $admin = \App\Models\User::whereHas('role', function($q) {
        $q->where('name', 'admin');
    })->first();
    echo "Admin Account:\n";
    echo "  Email: {$admin->email}\n";
    echo "  Name: {$admin->name}\n";
    echo "  Role: {$admin->role->name}\n\n";
}

// Check if we can create a test violation
if ($studentCount > 0 && $adminCount > 0) {
    $student = \App\Models\User::whereNotNull('student_id')->first();
    $admin = \App\Models\User::whereHas('role', function($q) {
        $q->where('name', 'admin');
    })->first();
    
    echo "Testing violation creation...\n";
    try {
        $violation = \App\Models\Violation::create([
            'student_id' => $student->id,
            'reported_by' => $admin->id,
            'violation_type' => 'Test Violation - DELETE ME',
            'level' => 'Level 1',
            'description' => 'This is a test violation to verify the system works',
            'severity' => 'minor',
            'status' => 'pending',
            'violation_date' => now(),
        ]);
        
        echo "✓ SUCCESS! Test violation created (ID: {$violation->id})\n";
        echo "  Student: {$student->name} ({$student->student_id})\n";
        echo "  Type: {$violation->violation_type}\n\n";
        
        // Clean up
        $violation->delete();
        echo "✓ Test violation deleted (cleanup)\n";
        echo "\n✅ SYSTEM IS WORKING CORRECTLY!\n";
        echo "   You should be able to create violations through the web interface.\n\n";
        
    } catch (\Exception $e) {
        echo "✗ ERROR: " . $e->getMessage() . "\n\n";
        echo "Possible issues:\n";
        echo "1. Database connection problem\n";
        echo "2. Missing required fields in violations table\n";
        echo "3. Foreign key constraint issues\n";
    }
} else {
    echo "⚠️  Cannot test - missing students or admin\n\n";
}

echo "=== CHECK COMPLETE ===\n";
