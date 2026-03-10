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
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('hire_date');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->after('date_of_birth');
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable()->after('gender');
            $table->text('address')->nullable()->after('marital_status');
            $table->string('profile_picture')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->dropColumn(['phone', 'date_of_birth', 'gender', 'marital_status', 'address', 'profile_picture']);
        });
    }
};
