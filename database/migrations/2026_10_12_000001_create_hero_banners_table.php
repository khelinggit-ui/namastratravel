<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->string('alt_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        $storedBanners = DB::table('settings')->where('key', 'hero_banners')->value('value');
        $banners = json_decode((string) $storedBanners, true);

        if (is_array($banners)) {
            foreach (array_values($banners) as $sort => $image) {
                if (! is_string($image) || $image === '') {
                    continue;
                }

                DB::table('hero_banners')->insert([
                    'title' => 'Banner Hero '.($sort + 1),
                    'image' => $image,
                    'alt_text' => null,
                    'is_active' => true,
                    'sort' => $sort,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_banners');
    }
};
