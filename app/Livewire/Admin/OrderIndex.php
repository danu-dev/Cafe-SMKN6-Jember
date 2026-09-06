<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\SaldoTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class OrderIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $pengirimanFilter = '';

    #[Url]
    public string $pembayaranFilter = '';

    #[Url]
    public string $dateFilter = '';

    // Modal state
    public bool $showDetailModal = false;
    public ?Order $selectedOrder = null;

    public bool $showAssignKurirModal = false;
    public ?int $selectedKurirId = null;

    public function resetFilter(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->pengirimanFilter = '';
        $this->pembayaranFilter = '';
        $this->dateFilter = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPengirimanFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPembayaranFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateFilter(): void
    {
        $this->resetPage();
    }

    public function openDetail(int $orderId): void
    {
        $this->selectedOrder = Order::with(['user', 'kurir', 'items.menu', 'saldoTransactions'])->findOrFail($orderId);
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedOrder = null;
    }

    public function openAssignKurir(int $orderId): void
    {
        $this->selectedOrder = Order::findOrFail($orderId);
        $this->selectedKurirId = $this->selectedOrder->kurir_id;
        $this->showAssignKurirModal = true;
    }

    public function closeAssignKurir(): void
    {
        $this->showAssignKurirModal = false;
        $this->selectedOrder = null;
        $this->selectedKurirId = null;
    }

    public function assignKurir(): void
    {
        if (! $this->selectedOrder || ! $this->selectedKurirId) {
            return;
        }

        $this->selectedOrder->update([
            'kurir_id' => $this->selectedKurirId,
            'status' => $this->selectedOrder->status === 'menunggu' ? 'diproses' : $this->selectedOrder->status,
        ]);

        $kurir = User::find($this->selectedKurirId);
        if ($kurir) {
            $lokasiAntar = ($this->selectedOrder->ruangan_tujuan ?? 'Ruang') . ' - Kelas ' . ($this->selectedOrder->kelas_tujuan ?? '-') . ' ' . ($this->selectedOrder->jurusan_tujuan ?? '');
            $kurir->notify(new \App\Notifications\CafeNotification(
                title: 'Tugas Pengantaran Baru',
                message: "Anda ditugaskan mengantar pesanan #{$this->selectedOrder->kode_pesanan} ke {$lokasiAntar}",
                type: 'delivery',
                actionUrl: route('kurir.deliveries.index')
            ));
        }

        // Notif ke Siswa
        if ($this->selectedOrder->user) {
            $this->selectedOrder->user->notify(new \App\Notifications\CafeNotification(
                title: 'Kurir Ditugaskan',
                message: "Pesanan #{$this->selectedOrder->kode_pesanan} akan diantar oleh kurir {$kurir->name}",
                type: 'order',
                actionUrl: route('siswa.orders.index')
            ));
        }

        $this->closeAssignKurir();
        session()->flash('message', 'Kurir berhasil ditugaskan.');
    }

    public function updateStatus(int $orderId, string $newStatus): void
    {
        $validStatuses = ['menunggu', 'diproses', 'siap', 'diantar', 'selesai', 'dibatalkan'];
        if (! in_array($newStatus, $validStatuses)) {
            return;
        }

        $updatedOrder = DB::transaction(function () use ($orderId, $newStatus) {
            $order = Order::with('user')->lockForUpdate()->findOrFail($orderId);

            // Auto refund if cancelled & paid with saldo
            if ($newStatus === 'dibatalkan' && $order->status !== 'dibatalkan') {
                if ($order->metode_pembayaran === 'saldo' && $order->status_pembayaran === 'sudah_dibayar') {
                    $user = User::lockForUpdate()->findOrFail($order->user_id);
                    $saldoSebelum = (float) $user->saldo;
                    $saldoSesudah = $saldoSebelum + $order->total_harga;

                    $user->update(['saldo' => $saldoSesudah]);

                    SaldoTransaction::create([
                        'user_id' => $user->id,
                        'admin_id' => Auth::id(),
                        'order_id' => $order->id,
                        'tipe' => 'refund',
                        'jumlah' => $order->total_harga,
                        'saldo_sebelum' => $saldoSebelum,
                        'saldo_sesudah' => $saldoSesudah,
                        'keterangan' => 'Refund pesanan #' . $order->kode_pesanan . ' dibatalkan',
                    ]);
                }
            }

            // Auto set status_pembayaran to sudah_dibayar when selesai
            $updates = ['status' => $newStatus];
            if ($newStatus === 'selesai' && $order->status_pembayaran === 'belum_dibayar') {
                $updates['status_pembayaran'] = 'sudah_dibayar';
            }

            $order->update($updates);

            // Kirim notifikasi status ke siswa
            if ($order->user) {
                $statusLabels = [
                    'diproses' => 'sedang diproses / dimasak di kantin',
                    'siap' => 'telah siap diambil / menunggu kurir',
                    'diantar' => 'sedang diantar oleh kurir ke kelas Anda',
                    'selesai' => 'telah selesai. Selamat menikmati!',
                    'dibatalkan' => 'telah dibatalkan.',
                ];
                $msg = "Pesanan #{$order->kode_pesanan} " . ($statusLabels[$newStatus] ?? "diubah ke {$newStatus}.");
                $order->user->notify(new \App\Notifications\CafeNotification(
                    title: 'Update Status Pesanan',
                    message: $msg,
                    type: 'order',
                    actionUrl: route('siswa.orders.index')
                ));
            }

            return $order;
        });

        // Refresh detail modal if open
        if ($this->showDetailModal && $this->selectedOrder && $this->selectedOrder->id === $orderId) {
            $this->selectedOrder = Order::with(['user', 'kurir', 'items.menu', 'saldoTransactions'])->find($orderId);
        }

        session()->flash('message', "Status pesanan #{$updatedOrder->kode_pesanan} diubah ke {$newStatus}.");
    }

    public function render()
    {
        $query = Order::with(['user', 'kurir', 'items.menu'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('kode_pesanan', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($u) {
                            $u->where('name', 'like', "%{$this->search}%")
                                ->orWhere('username', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->pengirimanFilter, fn ($q) => $q->where('tipe_pengiriman', $this->pengirimanFilter))
            ->when($this->pembayaranFilter, fn ($q) => $q->where('metode_pembayaran', $this->pembayaranFilter))
            ->when($this->dateFilter, fn ($q) => $q->whereDate('created_at', $this->dateFilter))
            ->latest();

        $orders = $query->paginate(10);
        $kurirs = User::where('role', 'kurir')->where('is_active', true)->get();

        // Summary counts
        $counts = [
            'all' => Order::count(),
            'menunggu' => Order::where('status', 'menunggu')->count(),
            'diproses' => Order::where('status', 'diproses')->count(),
            'siap' => Order::where('status', 'siap')->count(),
            'diantar' => Order::where('status', 'diantar')->count(),
            'selesai' => Order::where('status', 'selesai')->count(),
            'dibatalkan' => Order::where('status', 'dibatalkan')->count(),
        ];

        return view('livewire.admin.order-index', [
            'orders' => $orders,
            'kurirs' => $kurirs,
            'counts' => $counts,
        ]);
    }
}
