<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete(); // Tied to the active term
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();

            // References the 'users' table where role = Student
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();

            $table->date('date');
            $table->enum('status', ['PRESENT', 'ABSENT', 'LATE'])->default('PRESENT');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Prevent double-marking a student on the same day
            $table->unique(['student_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
