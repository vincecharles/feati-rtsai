<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->string('position')->nullable()->after('date_hired'); // Teacher, Program Chair, Department Head, Security, OSA, Super Admin
            $table->string('department')->nullable()->after('position'); // College/Department name
        });
    }

    public function down(): void {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn(['position', 'department']);
        });
    }
};
