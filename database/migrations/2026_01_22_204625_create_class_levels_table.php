<?php

use App\Models\School;
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
        Schema::create('class_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(School::class)
            ->constrained()
            ->cascadeOnDelete();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();


            // COMPOSITE UNIQUE KEY
            // Prevents duplicates like "Grade 1" appearing twice within the same school.
            $table->unique(['school_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_levels');
    }
};
