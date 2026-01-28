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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(School::class)
            ->constrained()
            ->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // COMPOSITE UNIQUE KEY
        // Allows School A and School B to both have "MATH101"
        // But prevents School A from having "MATH101" twice.
            $table->unique(['school_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
