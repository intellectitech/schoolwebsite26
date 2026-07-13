<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // e.g. "The Heritage Library"
            $table->text('description')->nullable();
            $table->string('image_url');
            // The design shows one large "hero" facility card next to two smaller stacked ones.
            // A flag keeps that editorial choice admin-controlled rather than hardcoded to "first row".
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};