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
        Schema::table('users', function (Blueprint $table) {
            // Add unique index to student_id if students table has references
            if (Schema::hasColumn('users', 'student_id')) {
                $table->unique('student_id', 'unique_student_id');
            }
            
            // Add index to email for faster lookups
            if (Schema::hasColumn('users', 'email')) {
               
                $table->index('email', 'idx_email');
            }
            
            // Add index to password column (if needed for queries)
            if (Schema::hasColumn('users', 'password')) {
              
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
