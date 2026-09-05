<?php

use App\Http\Controllers\XenditWebhookController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public webhook endpoint for Xendit Payment Gateway
Route::post('/api/xendit/webhook', [XenditWebhookController::class, 'handle'])->name('xendit.webhook');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('home');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'kurir' => redirect()->route('kurir.dashboard'),
            default => redirect()->route('siswa.dashboard'),
        };
    })->name('dashboard');

    // Admin routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
        Route::get('/menu', \App\Livewire\Admin\MenuIndex::class)->name('menu.index');
        Route::get('/kategori', \App\Livewire\Admin\KategoriIndex::class)->name('kategori.index');

        Route::get('/orders', \App\Livewire\Admin\OrderIndex::class)->name('orders.index');
        Route::get('/kurir', \App\Livewire\Admin\KurirIndex::class)->name('kurir.index');
        Route::get('/users', \App\Livewire\Admin\UserIndex::class)->name('users.index');
        Route::get('/verifikasi-kartu', \App\Livewire\Admin\VerifikasiKartu::class)->name('verifikasi.index');
        Route::get('/reports', \App\Livewire\Admin\ReportIndex::class)->name('reports.index');
        Route::get('/settings', \App\Livewire\Admin\StoreSettings::class)->name('settings.index');
    });

    // Kurir routes
    Route::middleware(['role:kurir'])->prefix('kurir')->name('kurir.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Kurir\Dashboard::class)->name('dashboard');
        Route::get('/pengantaran', \App\Livewire\Kurir\DeliveryIndex::class)->name('deliveries.index');
    });

    // Siswa routes
    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Siswa\Dashboard::class)->name('dashboard');
        Route::get('/menu', \App\Livewire\Siswa\MenuOrder::class)->name('menu.index');
        Route::get('/orders', \App\Livewire\Siswa\OrderIndex::class)->name('orders.index');
        Route::get('/saldo', \App\Livewire\Siswa\SaldoIndex::class)->name('saldo.index');
    });
});
