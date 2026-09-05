<?php

namespace App\Livewire\Admin;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        $today = Carbon::today();

        // Summary Stats
        $pendapatanHariIni = Order::whereDate('created_at', $today)
            ->whereIn('status', ['diproses', 'siap', 'diantar', 'selesai'])
            ->sum('total_harga');

        $pesananHariIni = Order::whereDate('created_at', $today)->count();
        $pesananPerluProses = Order::whereIn('status', ['menunggu', 'diproses'])->count();
        $totalMenuAktif = Menu::where('is_available', true)->count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalKurir = User::where('role', 'kurir')->count();

        // Recent Orders
        $pesananTerbaru = Order::with(['user', 'kurir', 'items.menu'])
            ->latest()
            ->take(6)
            ->get();

        // 7 Days Sales Trend
        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $total = Order::whereDate('created_at', $date)
                ->whereIn('status', ['selesai'])
                ->sum('total_harga');
            $count = Order::whereDate('created_at', $date)->count();

            $salesTrend[] = [
                'day' => $date->locale('id')->isoFormat('ddd'),
                'date' => $date->format('d M'),
                'total' => (float) $total,
                'count' => $count,
            ];
        }

        return view('livewire.admin.dashboard', [
            'pendapatanHariIni' => $pendapatanHariIni,
            'pesananHariIni' => $pesananHariIni,
            'pesananPerluProses' => $pesananPerluProses,
            'totalMenuAktif' => $totalMenuAktif,
            'totalSiswa' => $totalSiswa,
            'totalKurir' => $totalKurir,
            'pesananTerbaru' => $pesananTerbaru,
            'salesTrend' => $salesTrend,
        ]);
    }
}
