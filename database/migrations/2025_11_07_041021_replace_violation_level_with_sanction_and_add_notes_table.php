<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('violations', function (Blueprint $table) {
            // Only drop columns if they exist
            if (Schema::hasColumn('violations', 'level')) {
                $table->dropColumn('level');
            }
            if (Schema::hasColumn('violations', 'severity')) {
                $table->dropColumn('severity');
            }
        });

        Schema::table('violations', function (Blueprint $table) {
            if (!Schema::hasColumn('violations', 'sanction')) {
                $table->enum('sanction', [
                    'Disciplinary Citation (E)',
                    'Suspension (D)',
                    'Preventive Suspension (C)',
                    'Exclusion (B)',
                    'Expulsion (A)'
                ])->after('violation_type')->nullable();
            }
        });

        if (!Schema::hasTable('violation_notes')) {
            Schema::create('violation_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('violation_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->text('note');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('violation_notes');
        
        Schema::table('violations', function (Blueprint $table) {
            $table->dropColumn('sanction');
            $table->enum('level', ['Level 1', 'Level 2', 'Level 3', 'Expulsion'])->default('Level 1');
            $table->enum('severity', ['minor', 'moderate', 'major', 'severe'])->default('minor');
        });
    }
};
