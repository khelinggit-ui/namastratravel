<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('tour_name')->nullable();
            $table->string('whatsapp');
            $table->string('email');
            $table->text('destination');
            $table->string('pax')->nullable();
            $table->string('planned_date')->nullable();
            $table->enum('status', ['baru', 'dihubungi', 'selesai'])->default('baru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};