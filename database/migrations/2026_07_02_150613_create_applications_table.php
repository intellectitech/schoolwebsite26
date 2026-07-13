<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // Step 1: Personal Information
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->string('email');
            $table->string('phone');

            // Step 2: Academic History
            $table->string('current_school');
            $table->string('grade_applying_for');
            $table->string('current_gpa')->nullable(); // kept as string: GPA scales vary (4.0, letter grades, etc.)
            $table->string('transcript_path')->nullable();

            // Step 3: Statement of Intent
            $table->text('personal_statement');

            // Admin review workflow — not in the original design, but submissions
            // are useless to staff without a way to track review status.
            $table->enum('status', ['pending', 'reviewed', 'accepted', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};