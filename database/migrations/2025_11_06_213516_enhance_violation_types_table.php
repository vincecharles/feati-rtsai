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
        Schema::table('violation_types', function (Blueprint $table) {
            $table->string('code', 10)->after('id'); // e.g., "1", "2", "26", "MINOR-1"
            $table->string('name'); // e.g., "Assaulting a University authority"
            $table->text('description')->nullable(); // Full description
            $table->enum('category', ['major', 'minor'])->default('major');
            $table->string('offense_class')->nullable(); // e.g., "1-21", "22-24", "MINOR"
            $table->json('penalties')->nullable(); // Penalties by offense number
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('violation_types', function (Blueprint $table) {
            $table->dropColumn(['code', 'name', 'description', 'category', 'offense_class', 'penalties']);
        });
    }
};
