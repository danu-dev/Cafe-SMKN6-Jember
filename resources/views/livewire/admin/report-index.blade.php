<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Laporan Penjualan') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Analisis performa penjualan, menu terlaris, dan unduh laporan.') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button
                wire:click="exportCsv"
                wire:loading.attr="disabled"
                class="inline-flex items-center px-3.5 py-2 border border-gray-200 bg-white text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-50 shadow-sm transition gap-1.5 disabled:opacity-50"
            >
                <svg wire:loading.remove wire:target="exportCsv" class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <svg wire:loading wire:target="exportCsv" class="size-4 animate-spin text-emerald-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Export CSV
            </button>
            <button
                wire:click="exportPdf"
                wire:loading.attr="disabled"
                class="inline-flex items-center px-3.5 py-2 bg-brand-600 text-white text-xs font-semibold rounded-xl hover:bg-brand-700 shadow-sm shadow-brand-500/30 transition gap-1.5 disabled:opacity-50"
            >
                <svg wire:loading.remove wire:target="exportPdf" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <svg wire:loading wire:target="exportPdf" class="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Export PDF
            </button>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="flex flex-wrap gap-2">
            @foreach([
                'today' => 'Hari Ini',
                'this_week' => 'Minggu Ini',
                'this_month' => 'Bulan Ini',
                'this_year' => 'Tahun Ini',
            ] as $key => $label)
                <button
                    wire:click="setPeriod('{{ $key }}')"
                    class="px-3 py-1.5 text-xs font-semibold rounded-lg transition {{ $period === $key ? 'bg-brand-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <input
                type="date"
                wire:model.live="startDate"
                class="border border-gray-200 rounded-lg text-xs py-1.5 px-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white"
            />
            <span class="text-xs text-gray-400">s/d</span>
            <input
                type="date"
                wire:model.live="endDate"
                class="border border-gray-200 rounded-lg text-xs py-1.5 px-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white"
            />
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold uppercase text-gray-400">Total Pendapatan</div>
            <div class="text-2xl font-black text-brand-600 mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Dari pesanan selesai</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold uppercase text-gray-400">Total Pesanan</div>
            <div class="text-2xl font-black text-gray-900 mt-1">{{ $totalOrders }}</div>
            <div class="text-xs text-emerald-600 mt-1 font-medium">{{ $completedOrders }} selesai • {{ $cancelledOrders }} batal</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold uppercase text-gray-400">Rata-rata Pesanan</div>
            <div class="text-2xl font-black text-blue-600 mt-1">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Nilai per transaksi</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold uppercase text-gray-400">Tingkat Keberhasilan</div>
            <div class="text-2xl font-black text-purple-600 mt-1">
                {{ $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0 }}%
            </div>
            <div class="text-xs text-gray-500 mt-1">Pesanan terselesaikan</div>
        </div>
    </div>

    {{-- 2 Columns: Top Menus & Category Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Top 5 Menus --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="size-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                </svg>
                Top 5 Menu Terlaris
            </h3>
            <div class="space-y-4">
                @forelse($topMenus as $index => $item)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="size-6 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <div class="font-bold text-sm text-gray-900">{{ $item->menu->nama ?? 'Menu Dihapus' }}</div>
                                <div class="text-xs text-gray-400">{{ $item->menu->kategori->nama ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">{{ $item->total_qty }} porsi</div>
                            <div class="text-xs text-emerald-600 font-medium">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-8">Belum ada data penjualan pada periode ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Category Breakdown --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="size-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Penjualan per Kategori
            </h3>
            <div class="space-y-4">
                @forelse($kategoriSales as $kat)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-semibold text-gray-800">{{ $kat['nama'] }} ({{ $kat['qty'] }} item)</span>
                            <span class="font-bold text-brand-600">Rp {{ number_format($kat['revenue'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            @php
                                $pct = $totalRevenue > 0 ? ($kat['revenue'] / $totalRevenue) * 100 : 0;
                            @endphp
                            <div class="bg-brand-600 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-8">Belum ada data kategori.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Completed Orders Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-base">Riwayat Transaksi Terbaru</h3>
            <span class="text-xs text-gray-400">Menampilkan hingga 10 transaksi terakhir</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemesan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-mono font-bold text-gray-900 text-sm">
                                #{{ $order->kode_pesanan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->user->name ?? '-' }}</div>
                                @if($order->user && $order->user->kelas)
                                    <div class="text-xs text-gray-400">Kelas {{ $order->user->kelas }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 max-w-xs truncate">
                                {{ $order->items->map(fn($i) => ($i->menu->nama ?? 'Menu') . ' (' . $i->jumlah . 'x)')->join(', ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $stColors = [
                                        'selesai' => 'bg-emerald-50 text-emerald-700',
                                        'dibatalkan' => 'bg-rose-50 text-rose-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $stColors[$order->status] ?? 'bg-amber-50 text-amber-700' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-sm text-gray-900">
                                Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
                                Tidak ada transaksi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
