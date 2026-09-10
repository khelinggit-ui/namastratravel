<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->enum('category', ['domestik', 'mancanegara'])->default('domestik');
            $table->string('duration')->nullable();
            $table->unsignedBigInteger('price_start')->default(0);
            $table->string('location')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->json('highlights')->nullable();
            $table->string('image')->nullable();
            $table->string('tag')->nullable();
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};