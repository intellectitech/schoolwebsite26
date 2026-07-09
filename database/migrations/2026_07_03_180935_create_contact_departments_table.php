<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // e.g. "Admissions Office"
            $table->string('description')->nullable(); // e.g. "Prospective student inquiries"
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            // Renders as the red "Campus Security" emergency block instead of
            // a standard call/email card — a flag rather than a separate table
            // since it's the same shape of data, just styled differently.
            $table->boolean('is_emergency')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_departments');
    }
};