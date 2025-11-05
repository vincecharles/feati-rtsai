<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATABASE INFORMATION ===\n\n";

$dbName = config('database.connections.mysql.database');
$dbHost = config('database.connections.mysql.host');
$dbPort = config('database.connections.mysql.port');
$dbUser = config('database.connections.mysql.username');

echo "Laravel is connected to:\n";
echo "  Database: {$dbName}\n";
echo "  Host: {$dbHost}\n";
echo "  Port: {$dbPort}\n";
echo "  Username: {$dbUser}\n\n";

echo "Data in this database:\n";
echo "  - Users: " . \App\Models\User::count() . "\n";
echo "  - Students: " . \App\Models\User::whereNotNull('student_id')->count() . "\n";
echo "  - Employees: " . \App\Models\User::whereHas('profile')->count() . "\n";
echo "  - Violations: " . \App\Models\Violation::count() . "\n";
echo "  - Roles: " . \App\Models\Role::count() . "\n\n";

echo "⚠️  IMPORTANT:\n";
echo "  In MySQL Workbench, make sure you're looking at:\n";
echo "  DATABASE: '{$dbName}' (not 'feati_rtsai')\n\n";

echo "To view data in MySQL Workbench:\n";
echo "  1. Look for '{$dbName}' in the SCHEMAS panel\n";
echo "  2. Click on it to select it\n";
echo "  3. Expand Tables\n";
echo "  4. Right-click 'violations' → Select Rows\n\n";

echo "OR run this SQL query:\n";
echo "  USE {$dbName};\n";
echo "  SELECT * FROM violations ORDER BY id DESC LIMIT 10;\n";
echo "\n=== END ===\n";
