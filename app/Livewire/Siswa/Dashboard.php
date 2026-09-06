<?php

namespace App\Livewire\Siswa;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.siswa')]
class Dashboard extends Component
{
    public function render()
    {
        /** @var User $user */
        $user = Auth::user();
        $userId = $user->id;

        // Stat metrics
        $saldo = $user->saldo;

        $pesananAktifCount = Order::where('user_id', $userId)
            ->whereIn('status', ['menunggu', 'diproses', 'siap', 'diantar'])
            ->count();

        $pesananSelesaiCount = Order::where('user_id', $userId)
            ->where('status', 'selesai')
            ->count();

        $totalPengeluaran = Order::where('user_id', $userId)
            ->where('status', 'selesai')
            ->sum('total_harga');

        // Pesanan Aktif yang sedang berjalan
        $activeOrders = Order::with(['kurir', 'items.menu'])
            ->where('user_id', $userId)
            ->whereIn('status', ['menunggu', 'diproses', 'siap', 'diantar'])
            ->latest()
            ->get();

        // 4 Menu Rekomendasi / Tersedia
        $featuredMenus = Menu::with('kategori')
            ->where('is_available', true)
            ->latest()
            ->take(4)
            ->get();

        return view('livewire.siswa.dashboard', [
            'saldo' => $saldo,
            'pesananAktifCount' => $pesananAktifCount,
            'pesananSelesaiCount' => $pesananSelesaiCount,
            'totalPengeluaran' => $totalPengeluaran,
            'activeOrders' => $activeOrders,
            'featuredMenus' => $featuredMenus,
        ]);
    }
}
