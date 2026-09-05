<?php

namespace App\Livewire\Kurir;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.kurir')]
class DeliveryIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $dateFilter = '';

    public bool $showDetailModal = false;
    public ?Order $selectedOrder = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->dateFilter = '';
        $this->resetPage();
    }

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

        $query = Order::with(['user', 'items.menu'])
            ->where('kurir_id', $kurirId)
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('kode_pesanan', 'like', "%{$this->search}%")
                        ->orWhere('kelas_tujuan', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($u) {
                            $u->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFilter, fn ($q) => $q->whereDate('created_at', $this->dateFilter))
            ->latest();

        $orders = $query->paginate(10);

        $counts = [
            'all' => Order::where('kurir_id', $kurirId)->count(),
            'siap' => Order::where('kurir_id', $kurirId)->where('status', 'siap')->count(),
            'diantar' => Order::where('kurir_id', $kurirId)->where('status', 'diantar')->count(),
            'selesai' => Order::where('kurir_id', $kurirId)->where('status', 'selesai')->count(),
        ];

        return view('livewire.kurir.delivery-index', [
            'orders' => $orders,
            'counts' => $counts,
        ]);
    }
}
