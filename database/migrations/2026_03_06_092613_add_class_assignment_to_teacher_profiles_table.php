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
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->foreignId('class_level_id')->nullable()->constrained()->nullOnDelete()->after('school_id');
        $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete()->after('class_level_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->dropForeignIdFor('class_level_id');
            $table->dropForeignIdFor('section_id');
            $table->dropColumn(['class_level_id', 'section_id']);
        });
    }
};
