<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pathways', function (Blueprint $table) {
            $table->id();
            $table->string('title');            // e.g. "Early Childhood"
            $table->text('description');
            $table->string('image_url');
            $table->string('link_url')->nullable(); // "Learn More" target; nullable until dedicated pages exist
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pathways');
    }
};