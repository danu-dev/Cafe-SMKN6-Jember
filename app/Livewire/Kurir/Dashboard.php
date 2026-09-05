<?php

namespace App\Livewire\Kurir;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.kurir')]
class Dashboard extends Component
{
    public bool $showDetailModal = false;
    public ?Order $selectedOrder = null;

    public function openDetail(int $orderId): void
    {
        $this->selectedOrder = Order::with(['user', 'items.menu'])
            ->where('kurir_id', Auth::id())
            ->findOrFail($orderId);
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedOrder = null;
    }

    public function updateStatus(int $orderId, string $newStatus): void
    {
        $validTransitions = [
            'siap' => 'diantar',
            'diantar' => 'selesai',
        ];

        $order = Order::where('kurir_id', Auth::id())->findOrFail($orderId);

        if (! isset($validTransitions[$order->status]) || $validTransitions[$order->status] !== $newStatus) {
            session()->flash('error', 'Transisi status tidak diizinkan.');
            return;
        }

        DB::transaction(function () use ($order, $newStatus) {
            $updates = ['status' => $newStatus];

            if ($newStatus === 'selesai' && $order->status_pembayaran === 'belum_dibayar') {
                $updates['status_pembayaran'] = 'sudah_dibayar';
            }

            $order->update($updates);

            if ($order->user) {
                $msg = $newStatus === 'diantar' 
                    ? "Kurir sedang mengantarkan pesanan #{$order->kode_pesanan} ke kelas Anda." 
                    : "Pesanan #{$order->kode_pesanan} telah sampai ke kelas Anda. Selamat menikmati!";

                $order->user->notify(new \App\Notifications\CafeNotification(
                    title: $newStatus === 'diantar' ? 'Pesanan Sedang Diantar' : 'Pesanan Telah Tiba',
                    message: $msg,
                    type: 'delivery',
                    actionUrl: route('siswa.orders.index')
                ));
            }
        });

        if ($this->showDetailModal && $this->selectedOrder && $this->selectedOrder->id === $orderId) {
            $this->selectedOrder = Order::with(['user', 'items.menu'])->find($orderId);
        }

        $label = $newStatus === 'diantar' ? 'sedang diantar' : 'telah diselesaikan';
        session()->flash('message', "Pesanan #{$order->kode_pesanan} {$label}.");
    }

    public function render()
    {
        $kurirId = Auth::id();
        $today = Carbon::today();

        // 4 Stat Cards
        $pengantaranAktif = Order::where('kurir_id', $kurirId)
            ->whereIn('status', ['siap', 'diantar'])
            ->count();

        $selesaiHariIni = Order::where('kurir_id', $kurirId)
            ->where('status', 'selesai')
            ->whereDate('updated_at', $today)
            ->count();

        $totalPengantaran = Order::where('kurir_id', $kurirId)->count();

        $totalSelesai = Order::where('kurir_id', $kurirId)
            ->where('status', 'selesai')
            ->count();

        $tingkatKeberhasilan = $totalPengantaran > 0 ? round(($totalSelesai / $totalPengantaran) * 100, 1) : 0;

        // Tugas Aktif Mendesak (Siap / Diantar)
        $tugasAktif = Order::with(['user', 'items.menu'])
            ->where('kurir_id', $kurirId)
            ->whereIn('status', ['siap', 'diantar'])
            ->latest()
            ->get();

        // 5 Selesai Terakhir
        $riwayatTerbaru = Order::with(['user', 'items.menu'])
            ->where('kurir_id', $kurirId)
            ->where('status', 'selesai')
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Tren 7 Hari Terakhir
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Order::where('kurir_id', $kurirId)
                ->where('status', 'selesai')
                ->whereDate('updated_at', $date)
                ->count();

            $weeklyTrend[] = [
                'day' => $date->locale('id')->isoFormat('ddd'),
                'date' => $date->format('d M'),
                'count' => $count,
            ];
        }

        return view('livewire.kurir.dashboard', [
            'pengantaranAktif' => $pengantaranAktif,
            'selesaiHariIni' => $selesaiHariIni,
            'totalPengantaran' => $totalPengantaran,
            'tingkatKeberhasilan' => $tingkatKeberhasilan,
            'tugasAktif' => $tugasAktif,
            'riwayatTerbaru' => $riwayatTerbaru,
            'weeklyTrend' => $weeklyTrend,
        ]);
    }
}
