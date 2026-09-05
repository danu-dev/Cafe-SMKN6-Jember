<?php

namespace App\Livewire\Siswa;

use App\Models\Menu;
use App\Models\Order;
use App\Models\SaldoTransaction;
use App\Models\User;
use App\Services\XenditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.siswa')]
class OrderIndex extends Component
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
        $this->selectedOrder = Order::with(['kurir', 'items.menu', 'saldoTransactions'])
            ->where('user_id', Auth::id())
            ->findOrFail($orderId);
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedOrder = null;
    }

    public function cancelOrder(int $orderId): void
    {
        $order = Order::with('items')->where('user_id', Auth::id())->findOrFail($orderId);

        if ($order->status !== 'menunggu') {
            session()->flash('error', 'Pesanan hanya dapat dibatalkan saat status masih "Menunggu Konfirmasi".');
            return;
        }

        DB::transaction(function () use ($order) {
            // Restore stock
            foreach ($order->items as $item) {
                Menu::where('id', $item->menu_id)->increment('stok', $item->jumlah);
            }

            // Refund saldo if paid with saldo
            if ($order->metode_pembayaran === 'saldo' && $order->status_pembayaran === 'sudah_dibayar') {
                /** @var User $user */
                $user = User::lockForUpdate()->findOrFail(Auth::id());
                $saldoSebelum = (float) $user->saldo;
                $saldoSesudah = $saldoSebelum + $order->total_harga;

                $user->update(['saldo' => $saldoSesudah]);

                SaldoTransaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'tipe' => 'refund',
                    'jumlah' => $order->total_harga,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'keterangan' => 'Refund pembatalan pesanan #' . $order->kode_pesanan,
                ]);
            }

            $order->update(['status' => 'dibatalkan']);
        });

        if ($this->showDetailModal && $this->selectedOrder && $this->selectedOrder->id === $orderId) {
            $this->selectedOrder = Order::with(['kurir', 'items.menu', 'saldoTransactions'])->find($orderId);
        }

        session()->flash('message', "Pesanan #{$order->kode_pesanan} berhasil dibatalkan.");
    }

    public function render(XenditService $xendit)
    {
        $userId = Auth::id();

        // Auto-check pending Xendit orders for this user
        $pendingOrders = Order::where('user_id', $userId)
            ->where('metode_pembayaran', 'xendit')
            ->where('status_pembayaran', 'belum_dibayar')
            ->whereNotNull('xendit_invoice_id')
            ->where('status', '!=', 'dibatalkan')
            ->latest()
            ->take(5)
            ->get();

        foreach ($pendingOrders as $pendingOrder) {
            $invoiceData = $xendit->getInvoice($pendingOrder->xendit_invoice_id);
            if ($invoiceData) {
                $status = strtoupper($invoiceData['status'] ?? '');
                if ($status === 'PAID' || $status === 'SETTLED') {
                    $paymentMethod = $invoiceData['payment_method'] ?? ($invoiceData['payment_channel'] ?? 'Xendit');
                    $pendingOrder->update([
                        'status_pembayaran' => 'sudah_dibayar',
                        'xendit_payment_channel' => $paymentMethod,
                        'paid_at' => now(),
                        'status' => $pendingOrder->status === 'menunggu' ? 'diproses' : $pendingOrder->status,
                    ]);
                } elseif ($status === 'EXPIRED') {
                    DB::transaction(function () use ($pendingOrder) {
                        foreach ($pendingOrder->items as $item) {
                            Menu::where('id', $item->menu_id)->increment('stok', $item->jumlah);
                        }
                        $pendingOrder->update(['status' => 'dibatalkan']);
                    });
                }
            }
        }

        $query = Order::with(['kurir', 'items.menu'])
            ->where('user_id', $userId)
            ->when($this->search, function ($q) {
                $q->where('kode_pesanan', 'like', "%{$this->search}%");
            })
            ->when($this->statusFilter, function ($q) {
                if ($this->statusFilter === 'aktif') {
                    $q->whereIn('status', ['menunggu', 'diproses', 'siap', 'diantar']);
                } else {
                    $q->where('status', $this->statusFilter);
                }
            })
            ->when($this->dateFilter, fn ($q) => $q->whereDate('created_at', $this->dateFilter))
            ->latest();

        $orders = $query->paginate(10);

        $counts = [
            'all' => Order::where('user_id', $userId)->count(),
            'aktif' => Order::where('user_id', $userId)->whereIn('status', ['menunggu', 'diproses', 'siap', 'diantar'])->count(),
            'selesai' => Order::where('user_id', $userId)->where('status', 'selesai')->count(),
            'dibatalkan' => Order::where('user_id', $userId)->where('status', 'dibatalkan')->count(),
        ];

        return view('livewire.siswa.order-index', [
            'orders' => $orders,
            'counts' => $counts,
        ]);
    }
}
