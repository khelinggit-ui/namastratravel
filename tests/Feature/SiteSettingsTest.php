<?php

namespace Tests\Feature;

use App\Models\HeroBanner;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_settings_include_only_active_hero_banner_urls_in_display_order(): void
    {
        HeroBanner::create([
            'title' => 'Second banner',
            'image' => 'images/hero-two.webp',
            'is_active' => true,
            'sort' => 2,
        ]);
        HeroBanner::create([
            'title' => 'Inactive banner',
            'image' => 'images/hero-hidden.webp',
            'is_active' => false,
            'sort' => 0,
        ]);
        HeroBanner::create([
            'title' => 'First banner',
            'image' => 'images/hero-one.webp',
            'is_active' => true,
            'sort' => 1,
        ]);

        $this->getJson('/api/settings')
            ->assertOk()
            ->assertJsonPath('hero_banners.0', url('storage/images/hero-one.webp'))
            ->assertJsonPath('hero_banners.1', url('storage/images/hero-two.webp'))
            ->assertJsonCount(2, 'hero_banners');
    }

    public function test_admin_can_update_site_settings_without_changing_hero_banners(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        HeroBanner::create([
            'title' => 'Homepage banner',
            'image' => 'images/hero.webp',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.settings'), ['name' => 'Namastra Travel Baru'])
            ->assertRedirect(route('admin.settings'));

        $this->assertSame('Namastra Travel Baru', Setting::getMany(['name'])['name']);
        $this->assertDatabaseHas('hero_banners', ['title' => 'Homepage banner']);
    }
}
