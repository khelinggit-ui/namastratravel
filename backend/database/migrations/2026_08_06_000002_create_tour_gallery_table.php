<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_gallery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->unsignedInteger('sort')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_gallery');
    }
};