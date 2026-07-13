<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('step_number'); // "01", "02"... rendered, not just an index
            $table->string('title');                // e.g. "Inquiry"
            $table->text('description');
            $table->string('icon');                 // Material Symbols icon name
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_steps');
    }
};