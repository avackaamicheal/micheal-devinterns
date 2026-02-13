<?php

use App\Models\ClassLevel;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('admission_number')->nullable()->unique();
            $table->foreignIdFor(ClassLevel::class)->nullable()->constrained(); // Current Grade
            $table->foreignIdFor(Section::class)->nullable()->constrained();     // Current Section
            $table->date('date_of_birth')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
