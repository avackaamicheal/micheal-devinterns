<?php

use App\Models\ClassLevel;
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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(School::class)
            ->constrained()
            ->cascadeOnDelete();
            $table->foreignIdFor(ClassLevel::class)
            ->constrained()
            ->cascadeOnDelete();
            $table->string('name');
            $table->integer('capacity');
            $table->boolean('is_active')->default(true);
            $table->timestamps();


            // COMPOSITE UNIQUE KEY
            // A class cannot have two sections named "A".
            // Unique combination of: Class Level + Name
            $table->unique(['class_level_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
