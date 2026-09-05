<?php

namespace Tests\Feature;

use App\Livewire\Admin\KurirIndex;
use App\Livewire\Admin\OrderIndex;
use App\Livewire\Admin\ReportIndex;
use App\Livewire\Admin\StoreSettings;
use App\Livewire\Admin\UserIndex;
use App\Models\KategoriMenu;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kurir;
    protected User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'username' => 'admin_test',
        ]);

        $this->kurir = User::factory()->create([
            'role' => 'kurir',
            'username' => 'kurir_test',
            'is_active' => true,
        ]);

        $this->siswa = User::factory()->create([
            'role' => 'siswa',
            'username' => 'siswa_test',
            'kelas' => '11',
            'saldo' => 50000,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_orders_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Manajemen Pesanan');
    }

    public function test_admin_can_update_order_status()
    {
        $kategori = KategoriMenu::create(['nama' => 'Makanan', 'slug' => 'makanan']);
        $menu = Menu::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Nasi Goreng',
            'harga' => 15000,
            'stok' => 10,
            'is_available' => true,
        ]);

        $order = Order::create([
            'kode_pesanan' => 'ORD-12345',
            'user_id' => $this->siswa->id,
            'tipe_pengiriman' => 'ambil',
            'metode_pembayaran' => 'saldo',
            'status_pembayaran' => 'sudah_dibayar',
            'status' => 'menunggu',
            'total_harga' => 15000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_id' => $menu->id,
            'jumlah' => 1,
            'harga_satuan' => 15000,
            'subtotal' => 15000,
        ]);

        Livewire::actingAs($this->admin)
            ->test(OrderIndex::class)
            ->call('updateStatus', $order->id, 'diproses');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'diproses',
        ]);
    }

    public function test_admin_can_access_kurir_page_and_create_kurir()
    {
        $this->actingAs($this->admin)->get(route('admin.kurir.index'))->assertStatus(200);

        Livewire::actingAs($this->admin)
            ->test(KurirIndex::class)
            ->set('name', 'Kurir Baru')
            ->set('username', 'kurir_baru')
            ->set('email', 'kurir_baru@cafe.test')
            ->set('password', 'password123')
            ->set('is_active', true)
            ->call('save');

        $this->assertDatabaseHas('users', [
            'username' => 'kurir_baru',
            'role' => 'kurir',
        ]);
    }

    public function test_admin_can_access_user_page_and_topup_saldo()
    {
        $this->actingAs($this->admin)->get(route('admin.users.index'))->assertStatus(200);

        Livewire::actingAs($this->admin)
            ->test(UserIndex::class)
            ->call('openTopup', $this->siswa->id)
            ->set('topupAmount', '25000')
            ->set('topupKeterangan', 'Topup Uji Coba')
            ->call('processTopup');

        $this->assertDatabaseHas('users', [
            'id' => $this->siswa->id,
            'saldo' => 75000,
        ]);

        $this->assertDatabaseHas('saldo_transactions', [
            'user_id' => $this->siswa->id,
            'tipe' => 'topup',
            'jumlah' => 25000,
        ]);
    }

    public function test_admin_can_access_reports_and_filter()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Penjualan');

        Livewire::actingAs($this->admin)
            ->test(ReportIndex::class)
            ->call('setPeriod', 'this_month')
            ->assertStatus(200);
    }

    public function test_admin_can_manage_store_settings()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Toko');

        Livewire::actingAs($this->admin)
            ->test(StoreSettings::class)
            ->set('store_name', 'Kantin SMK Hebat')
            ->set('whatsapp_contact', '08123456789')
            ->call('save');

        $this->assertEquals('Kantin SMK Hebat', StoreSetting::get('store_name'));
        $this->assertEquals('08123456789', StoreSetting::get('whatsapp_contact'));
    }
}
