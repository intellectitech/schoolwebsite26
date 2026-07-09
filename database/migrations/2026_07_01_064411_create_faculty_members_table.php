<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculty_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');            // e.g. "Dean of Humanities"
            $table->text('bio')->nullable();
            $table->string('photo_url');
            // Lets the same faculty pool be reused elsewhere later (e.g. a full staff directory)
            // while only a subset is "spotlighted" on the Academics page.
            $table->boolean('is_spotlighted')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faculty_members');
    }
};