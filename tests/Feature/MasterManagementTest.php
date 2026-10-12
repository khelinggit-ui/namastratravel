<?php

namespace Tests\Feature;

use App\Models\HeroBanner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MasterManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_users_cannot_access_master_management(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.master.banners.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_hero_banners(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)
            ->get(route('admin.master.banners.index'))
            ->assertOk()
            ->assertSee('Master / Banner Hero');
        $this->get(route('admin.master.banners.create'))->assertOk();

        $image = UploadedFile::fake()->createWithContent(
            'hero.gif',
            base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7')
        );

        $this->post(route('admin.master.banners.store'), [
            'title' => 'Hero musim panas',
            'alt_text' => 'Pantai saat matahari terbenam',
            'sort' => 1,
            'is_active' => '1',
            'image' => $image,
        ])
            ->assertRedirect(route('admin.master.banners.index'));

        $banner = HeroBanner::firstOrFail();
        Storage::disk('public')->assertExists($banner->image);
        $oldImage = $banner->image;
        $this->get(route('admin.master.banners.edit', $banner))->assertOk();

        $this->put(route('admin.master.banners.update', $banner), [
            'title' => 'Hero diperbarui',
            'alt_text' => 'Banner baru',
            'sort' => 2,
            'is_active' => '1',
        ])->assertRedirect(route('admin.master.banners.index'));

        $banner->refresh();
        $this->assertSame('Hero diperbarui', $banner->title);
        $this->assertSame(2, $banner->sort);
        $this->assertSame($oldImage, $banner->image);

        $this->delete(route('admin.master.banners.destroy', $banner))
            ->assertRedirect(route('admin.master.banners.index'));

        $this->assertDatabaseMissing('hero_banners', ['id' => $banner->id]);
        Storage::disk('public')->assertMissing($oldImage);
    }

    public function test_admin_can_create_update_and_delete_admin_accounts(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);
        User::factory()->create(['email' => 'customer-only@example.test', 'is_admin' => false]);
        $this->get(route('admin.master.users.index'))
            ->assertOk()
            ->assertSee('Master / Management User')
            ->assertDontSee('customer-only@example.test');
        $this->get(route('admin.master.users.create'))->assertOk();

        $this->post(route('admin.master.users.store'), [
            'name' => 'Admin Baru',
            'email' => 'admin-baru@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('admin.master.users.index'));

        $managedUser = User::where('email', 'admin-baru@example.test')->firstOrFail();
        $this->get(route('admin.master.users.edit', $managedUser))->assertOk();
        $this->assertTrue($managedUser->is_admin);
        $this->assertTrue(Hash::check('secure-password', $managedUser->password));

        $this->put(route('admin.master.users.update', $managedUser), [
            'name' => 'Admin Diperbarui',
            'email' => 'admin-updated@example.test',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.master.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $managedUser->id,
            'name' => 'Admin Diperbarui',
            'email' => 'admin-updated@example.test',
            'is_admin' => true,
        ]);

        $this->delete(route('admin.master.users.destroy', $managedUser))
            ->assertRedirect(route('admin.master.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $managedUser->id]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->delete(route('admin.master.users.destroy', $admin))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
