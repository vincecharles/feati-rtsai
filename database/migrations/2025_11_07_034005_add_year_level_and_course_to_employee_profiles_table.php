<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            // Add course and year_level columns for students
            if (!Schema::hasColumn('employee_profiles', 'course')) {
                $table->string('course')->nullable()->after('program');
            }
            if (!Schema::hasColumn('employee_profiles', 'year_level')) {
                $table->string('year_level')->nullable()->after('course');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn(['course', 'year_level']);
        });
    }
};
