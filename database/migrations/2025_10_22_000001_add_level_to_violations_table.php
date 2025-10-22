<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('violations', function (Blueprint $table) {
            $table->enum('level', ['Level 1', 'Level 2', 'Level 3', 'Expulsion'])->default('Level 1')->after('violation_type');
        });
    }

    public function down(): void {
        Schema::table('violations', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
