<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE tours MODIFY itinerary TEXT NULL');
            return;
        }

        Schema::table('tours', function (Blueprint $table) {
            $table->text('itinerary')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE tours MODIFY itinerary JSON NULL');
            return;
        }

        Schema::table('tours', function (Blueprint $table) {
            $table->json('itinerary')->nullable()->change();
        });
    }
};
