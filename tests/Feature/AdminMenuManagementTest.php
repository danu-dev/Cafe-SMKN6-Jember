<?php

namespace Tests\Feature;

use App\Livewire\Admin\KategoriIndex;
use App\Livewire\Admin\MenuIndex;
use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminMenuManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_menu_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/menu');
        $response->assertStatus(200);
        $response->assertSee('Daftar Menu Cafe');
    }

    public function test_admin_can_create_category(): void
    {
        Livewire::actingAs($this->admin)
            ->test(KategoriIndex::class)
            ->set('nama', 'Dessert')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('kategori_menus', [
            'nama' => 'Dessert',
            'slug' => 'dessert',
        ]);
    }

    public function test_admin_can_create_and_toggle_menu(): void
    {
        $kategori = KategoriMenu::create(['nama' => 'Minuman', 'slug' => 'minuman']);

        Livewire::actingAs($this->admin)
            ->test(MenuIndex::class)
            ->set('kategori_id', $kategori->id)
            ->set('nama', 'Kopi Susu Gula Aren')
            ->set('harga', '12000')
            ->set('stok', 20)
            ->set('is_available', true)
            ->call('save')
            ->assertHasNoErrors();

        $menu = Menu::where('nama', 'Kopi Susu Gula Aren')->first();
        $this->assertNotNull($menu);
        $this->assertTrue((bool) $menu->is_available);

        // Toggle availability
        Livewire::actingAs($this->admin)
            ->test(MenuIndex::class)
            ->call('toggleAvailability', $menu->id);

        $this->assertFalse((bool) $menu->fresh()->is_available);
    }
}
