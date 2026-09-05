<?php

namespace App\Livewire\Admin;

use App\Models\KategoriMenu;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
class ReportIndex extends Component
{
    #[Url]
    public string $startDate = '';

    #[Url]
    public string $endDate = '';

    #[Url]
    public string $period = 'this_month'; // today, this_week, this_month, this_year, custom

    public function mount(): void
    {
        if (empty($this->startDate) || empty($this->endDate)) {
            $this->setPeriod('this_month');
        }
    }

    public function updatedPeriod(string $value): void
    {
        $this->setPeriod($value);
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $now = Carbon::now();

        match ($period) {
            'today' => [
                $this->startDate = $now->copy()->startOfDay()->format('Y-m-d'),
                $this->endDate = $now->copy()->endOfDay()->format('Y-m-d'),
            ],
            'this_week' => [
                $this->startDate = $now->copy()->startOfWeek()->format('Y-m-d'),
                $this->endDate = $now->copy()->endOfWeek()->format('Y-m-d'),
            ],
            'this_month' => [
                $this->startDate = $now->copy()->startOfMonth()->format('Y-m-d'),
                $this->endDate = $now->copy()->endOfMonth()->format('Y-m-d'),
            ],
            'this_year' => [
                $this->startDate = $now->copy()->startOfYear()->format('Y-m-d'),
                $this->endDate = $now->copy()->endOfYear()->format('Y-m-d'),
            ],
            default => null,
        };
    }

    public function exportCsv()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $orders = Order::with(['user', 'kurir', 'items.menu'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $filename = 'laporan-penjualan-' . $this->startDate . '-sd-' . $this->endDate . '.csv';

        return $this->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Pesanan', 'Tanggal', 'Pemesan', 'Kelas', 'Tipe Pengiriman',
                'Kurir', 'Metode Pembayaran', 'Status Pembayaran', 'Status Pesanan',
                'Items', 'Total Harga (Rp)',
            ]);

            foreach ($orders as $order) {
                $itemsStr = $order->items->map(fn ($i) => ($i->menu->nama ?? 'Menu Dihapus') . ' (' . $i->jumlah . 'x)')->join('; ');

                fputcsv($handle, [
                    $order->kode_pesanan,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user->name ?? '-',
                    $order->user->kelas ?? '-',
                    $order->tipe_pengiriman,
                    $order->kurir->name ?? '-',
                    $order->metode_pembayaran,
                    $order->status_pembayaran,
                    $order->status,
                    $itemsStr,
                    $order->total_harga,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        $orders = Order::with(['user', 'kurir', 'items.menu'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->get();

        $totalPendapatan = $orders->where('status', 'selesai')->sum('total_harga');
        $totalPesananSelesai = $orders->where('status', 'selesai')->count();
        $totalPesananBatal = $orders->where('status', 'dibatalkan')->count();

        return $this->streamDownload(function () use ($orders, $totalPendapatan, $totalPesananSelesai, $totalPesananBatal) {
            $pdf = Pdf::loadView('exports.report-pdf', [
                'orders' => $orders,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'totalPendapatan' => $totalPendapatan,
                'totalPesanan' => $orders->count(),
                'totalSelesai' => $totalPesananSelesai,
                'totalBatal' => $totalPesananBatal,
            ]);
            echo $pdf->output();
        }, 'laporan-penjualan-' . $this->startDate . '-sd-' . $this->endDate . '.pdf');
    }

    public function render()
    {
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        // Summary Stats
        $ordersQuery = Order::whereBetween('created_at', [$start, $end]);

        $totalRevenue = (clone $ordersQuery)->where('status', 'selesai')->sum('total_harga');
        $totalOrders = (clone $ordersQuery)->count();
        $completedOrders = (clone $ordersQuery)->where('status', 'selesai')->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'dibatalkan')->count();
        $avgOrderValue = $completedOrders > 0 ? $totalRevenue / $completedOrders : 0;

        // Top 5 Menus
        $topMenus = OrderItem::select('menu_id', DB::raw('SUM(jumlah) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$start, $end])->where('status', 'selesai'))
            ->with('menu.kategori')
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Breakdown per Kategori
        $kategoriSales = KategoriMenu::withCount(['menus'])
            ->get()
            ->map(function ($kategori) use ($start, $end) {
                $totalRevenue = OrderItem::whereHas('menu', fn ($m) => $m->where('kategori_id', $kategori->id))
                    ->whereHas('order', fn ($o) => $o->whereBetween('created_at', [$start, $end])->where('status', 'selesai'))
                    ->sum('subtotal');

                $totalQty = OrderItem::whereHas('menu', fn ($m) => $m->where('kategori_id', $kategori->id))
                    ->whereHas('order', fn ($o) => $o->whereBetween('created_at', [$start, $end])->where('status', 'selesai'))
                    ->sum('jumlah');

                return [
                    'nama' => $kategori->nama,
                    'revenue' => (float) $totalRevenue,
                    'qty' => (int) $totalQty,
                ];
            })
            ->sortByDesc('revenue');

        // Recent Completed Orders in Range
        $recentOrders = Order::with(['user', 'items.menu'])
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.admin.report-index', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'cancelledOrders' => $cancelledOrders,
            'avgOrderValue' => $avgOrderValue,
            'topMenus' => $topMenus,
            'kategoriSales' => $kategoriSales,
            'recentOrders' => $recentOrders,
        ]);
    }
}
