<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\XenditWebhookController;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\KategoriIndex;
use App\Livewire\Admin\KurirIndex;
use App\Livewire\Admin\MenuIndex;
use App\Livewire\Admin\OrderIndex as AdminOrderIndex;
use App\Livewire\Admin\ReportIndex;
use App\Livewire\Admin\StoreSettings;
use App\Livewire\Admin\UserIndex;
use App\Livewire\Admin\VerifikasiKartu;
use App\Livewire\Kurir\Dashboard as KurirDashboard;
use App\Livewire\Kurir\DeliveryIndex;
use App\Livewire\Siswa\Dashboard as SiswaDashboard;
use App\Livewire\Siswa\MenuOrder;
use App\Livewire\Siswa\OrderIndex as SiswaOrderIndex;
use App\Livewire\Siswa\SaldoIndex;
use Illuminate\Support\Facades\Route;

// Public webhook endpoint for Xendit Payment Gateway
Route::post('/api/xendit/webhook', [XenditWebhookController::class, 'handle'])->name('xendit.webhook');

Route::get('/', fn () => redirect()->route('dashboard'))->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/menu', MenuIndex::class)->name('menu.index');
        Route::get('/kategori', KategoriIndex::class)->name('kategori.index');
        Route::get('/orders', AdminOrderIndex::class)->name('orders.index');
        Route::get('/kurir', KurirIndex::class)->name('kurir.index');
        Route::get('/users', UserIndex::class)->name('users.index');
        Route::get('/verifikasi-kartu', VerifikasiKartu::class)->name('verifikasi.index');
        Route::get('/reports', ReportIndex::class)->name('reports.index');
        Route::get('/settings', StoreSettings::class)->name('settings.index');
    });

    // Kurir routes
    Route::middleware(['role:kurir'])->prefix('kurir')->name('kurir.')->group(function () {
        Route::get('/dashboard', KurirDashboard::class)->name('dashboard');
        Route::get('/pengantaran', DeliveryIndex::class)->name('deliveries.index');
    });

    // Siswa routes
    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', SiswaDashboard::class)->name('dashboard');
        Route::get('/menu', MenuOrder::class)->name('menu.index');
        Route::get('/orders', SiswaOrderIndex::class)->name('orders.index');
        Route::get('/saldo', SaldoIndex::class)->name('saldo.index');
    });
});
