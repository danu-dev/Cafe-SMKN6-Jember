<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Pesanan Saya') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Pantau status proses makanan dan riwayat seluruh transaksi Anda.') }}
            </p>
        </div>
        <a href="{{ route('siswa.menu.index') }}" class="inline-flex items-center px-4 py-2.5 bg-brand-600 text-white text-xs font-bold rounded-xl hover:bg-brand-700 shadow-sm shadow-brand-500/30 transition gap-2">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            + Buat Pesanan Baru
        </a>
    </div>

    {{-- Flash Message / Error --}}
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Status Filter Tabs --}}
    <div class="flex flex-wrap gap-2">
        <button
            wire:click="$set('statusFilter', '')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === '' ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Semua ({{ $counts['all'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'aktif')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'aktif' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Sedang Berjalan ({{ $counts['aktif'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'selesai')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'selesai' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Selesai ({{ $counts['selesai'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'dibatalkan')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'dibatalkan' ? 'bg-rose-600 text-white shadow-sm shadow-rose-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Dibatalkan ({{ $counts['dibatalkan'] }})
        </button>
    </div>

    {{-- Search & Date Filter Bar --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="w-full md:w-96 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari kode pesanan..."
                class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
            />
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <input
                type="date"
                wire:model.live="dateFilter"
                class="border border-gray-200 rounded-lg text-sm py-2 px-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white"
            />

            @if($search || $statusFilter || $dateFilter)
                <button
                    wire:click="resetFilter"
                    class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2.5 py-1 rounded-lg hover:bg-rose-50 border border-rose-200 transition shrink-0"
                >
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    {{-- Orders List --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" wire:poll.5s>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode & Waktu</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu Dipesan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengiriman & Kurir</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="openDetail({{ $order->id }})" class="text-left group">
                                    <div class="font-mono font-bold text-gray-900 text-sm group-hover:text-brand-600 transition">
                                        #{{ $order->kode_pesanan }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        {{ $order->created_at->format('d M, H:i') }} ({{ $order->created_at->diffForHumans() }})
                                    </div>
                                </button>
                            </td>

                            <td class="px-6 py-4 text-xs text-gray-600 max-w-xs truncate">
                                <div>
                                    <span class="font-semibold text-gray-800">{{ $order->items->count() }} item</span>
                                    <span class="text-gray-400"> • </span>
                                    <span>{{ $order->items->map(fn($i) => ($i->menu->nama ?? 'Menu') . ' (' . $i->jumlah . 'x)')->join(', ') }}</span>
                                </div>
                                <div class="font-bold text-gray-900 mt-0.5">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-bold text-gray-900">
                                    @if($order->tipe_pengiriman === 'antar')
                                        <span>Diantar ke {{ $order->ruangan_tujuan ?? 'Ruang' }}</span>
                                        <div class="text-[11px] text-gray-500 font-semibold">Kls {{ $order->kelas_tujuan ?? '-' }} {{ $order->jurusan_tujuan ? '('.$order->jurusan_tujuan.')' : '' }}</div>
                                    @else
                                        <span>Ambil Sendiri di Kantin</span>
                                    @endif
                                </div>
                                @if($order->kurir)
                                    <div class="text-xs text-blue-600 mt-0.5 font-medium">Kurir: {{ $order->kurir->name }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs font-semibold uppercase text-gray-700">
                                        {{ strtoupper($order->metode_pembayaran) }}
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium w-fit {{ $order->status_pembayaran === 'sudah_dibayar' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $order->status_pembayaran === 'sudah_dibayar' ? 'Lunas' : 'Belum Lunas' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusBadges = [
                                        'menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'siap' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'diantar' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'dibatalkan' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border capitalize {{ $statusBadges[$order->status] ?? 'bg-gray-50 text-gray-700' }}">
                                    {{ $order->status }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-1">
                                @if($order->metode_pembayaran === 'xendit' && $order->status_pembayaran === 'belum_dibayar' && $order->xendit_payment_url && $order->status !== 'dibatalkan')
                                    <a
                                        href="{{ $order->xendit_payment_url }}"
                                        target="_blank"
                                        class="inline-flex items-center px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition"
                                    >
                                        Bayar Xendit &rarr;
                                    </a>
                                @endif

                                @if($order->status === 'menunggu')
                                    <button
                                        wire:click="cancelOrder({{ $order->id }})"
                                        wire:confirm="Yakin ingin membatalkan pesanan ini? Saldo (jika pakai saldo) akan otomatis dikembalikan."
                                        class="text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-2.5 py-1.5 rounded-lg transition"
                                    >
                                        Batalkan
                                    </button>
                                @endif

                                <button
                                    wire:click="openDetail({{ $order->id }})"
                                    class="text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition"
                                >
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-gray-500 text-sm">
                                <div class="size-14 rounded-2xl bg-brand-50 text-brand-600 mx-auto flex items-center justify-center mb-3">
                                    <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </div>
                                <p class="text-base font-bold text-gray-900">Belum ada riwayat pesanan</p>
                                <p class="text-xs text-gray-400 mt-1 mb-4">Kamu belum pernah membuat pesanan di cafe.</p>
                                <a href="{{ route('siswa.menu.index') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-xs font-bold rounded-xl hover:bg-brand-700 shadow-sm transition">
                                    Pesan Sekarang &rarr;
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeDetail"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-brand-900 px-6 py-5 text-white flex justify-between items-center">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-bold font-mono">#{{ $selectedOrder->kode_pesanan }}</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-white/20">
                                    {{ $selectedOrder->status }}
                                </span>
                            </div>
                            <p class="text-xs text-brand-200 mt-0.5">{{ $selectedOrder->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <button wire:click="closeDetail" class="text-brand-300 hover:text-white transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                        {{-- Status Timeline --}}
                        @if($selectedOrder->status !== 'dibatalkan')
                            <div class="bg-brand-50/60 p-4 rounded-xl border border-brand-100">
                                <h4 class="text-xs font-semibold uppercase text-gray-500 mb-3">Status Pemrosesan</h4>
                                @php
                                    $allSteps = ['menunggu', 'diproses', 'siap', 'diantar', 'selesai'];
                                    if($selectedOrder->tipe_pengiriman === 'ambil') {
                                        $allSteps = ['menunggu', 'diproses', 'siap', 'selesai'];
                                    }
                                    $currentIndex = array_search($selectedOrder->status, $allSteps);
                                @endphp
                                <div class="flex items-center justify-between text-center relative">
                                    @foreach($allSteps as $idx => $step)
                                        <div class="flex-1 flex flex-col items-center relative z-10">
                                            <div class="size-6 rounded-full flex items-center justify-center text-[10px] font-bold {{ $idx <= $currentIndex ? 'bg-brand-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                                {{ $idx + 1 }}
                                            </div>
                                            <span class="text-[10px] font-semibold mt-1 capitalize {{ $idx <= $currentIndex ? 'text-brand-900' : 'text-gray-400' }}">
                                                {{ $step }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 font-medium">
                                Pesanan ini telah dibatalkan.
                            </div>
                        @endif

                        {{-- Info Pengiriman --}}
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tipe Pengiriman:</span>
                                <span class="font-bold text-gray-900">
                                    {{ $selectedOrder->tipe_pengiriman === 'antar' ? 'Diantar Kurir ke ' . ($selectedOrder->ruangan_tujuan ?? 'Ruang') . ' (Kelas ' . ($selectedOrder->kelas_tujuan ?? '-') . ' ' . ($selectedOrder->jurusan_tujuan ?? '') . ')' : 'Ambil Sendiri di Kantin' }}
                                </span>
                            </div>
                            @if($selectedOrder->kurir)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Kurir Pengantar:</span>
                                    <span class="font-bold text-blue-700">{{ $selectedOrder->kurir->name }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-gray-500">Metode Pembayaran:</span>
                                <span class="font-bold text-gray-900 uppercase">{{ $selectedOrder->metode_pembayaran }} ({{ $selectedOrder->status_pembayaran === 'sudah_dibayar' ? 'Lunas' : 'Belum Lunas' }})</span>
                            </div>
                            @if($selectedOrder->catatan)
                                <div class="pt-2 border-t border-gray-200 text-gray-600">
                                    <strong>Catatan:</strong> {{ $selectedOrder->catatan }}
                                </div>
                            @endif
                        </div>

                        {{-- Item List --}}
                        <div>
                            <h4 class="text-xs font-semibold uppercase text-gray-400 tracking-wider mb-2">Item yang Dipesan</h4>
                            <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden">
                                @foreach($selectedOrder->items as $item)
                                    <div class="p-3 bg-white flex justify-between items-center text-sm">
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $item->menu->nama ?? 'Menu Dihapus' }}</div>
                                            <div class="text-xs text-gray-400">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }} × {{ $item->jumlah }}</div>
                                        </div>
                                        <div class="font-bold text-gray-900 text-sm">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between items-center pt-3 px-1 text-sm font-black text-gray-900">
                                <span>Total Pembayaran</span>
                                <span class="text-brand-600">Rp {{ number_format($selectedOrder->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-100">
                        <button
                            wire:click="closeDetail"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-100 transition"
                        >
                            Tutup
                        </button>

                        @if($selectedOrder->status === 'menunggu')
                            <button
                                wire:click="cancelOrder({{ $selectedOrder->id }})"
                                wire:confirm="Batalkan pesanan ini?"
                                class="px-4 py-2 bg-rose-600 text-white rounded-lg text-xs font-semibold hover:bg-rose-700 transition"
                            >
                                Batalkan Pesanan
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
