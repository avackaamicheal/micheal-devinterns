<?php

use App\Models\School;
use App\Models\Section;
use App\Models\Subject;
use App\Models\TeacherProfile;
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
        Schema::create('classroom_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(School::class)->constrained(); // Multitenancy
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete;
            $table->foreignIdFor(Section::class)->constrained();
            $table->foreignIdFor(Subject::class)->constrained();
            $table->timestamps();

            // Constraint: A teacher can't be assigned the same subject in the same section twice
            $table->unique(['teacher_id', 'section_id', 'subject_id'], 'unique_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_assignments');
    }
};
