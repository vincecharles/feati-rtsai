<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            // Add QR code field for scanning
            $table->string('qr_code', 100)->nullable()->unique()->after('student_number');
            
            // Index for faster lookups
            $table->index('qr_code');
        });

        // Generate QR codes for existing students
        DB::statement("
            UPDATE student_profiles 
            SET qr_code = CONCAT('STD-', student_number) 
            WHERE qr_code IS NULL AND student_number IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropIndex(['qr_code']);
            $table->dropColumn('qr_code');
        });
    }
};
