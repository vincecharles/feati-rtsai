<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('employee_number')->unique()->nullable(); // YY-XXXXXXXX
            $table->date('date_hired')->nullable();

            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('preferred_name')->nullable();

            $table->string('sex')->nullable();    // Male/Female
            $table->string('gender')->nullable(); // Male, Female, Non-binary, ...

            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();

            $table->string('civil_status')->nullable();
            $table->string('nationality')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();

            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();

            $table->string('emergency_name')->nullable();
            $table->string('emergency_relationship')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->text('emergency_address')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('employee_profiles');
    }
};

