<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Daftar Pengantaran') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Kelola dan proses seluruh tugas pengantaran pesanan Anda.') }}
            </p>
        </div>
    </div>

    {{-- Flash Message --}}
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

    {{-- Status Filter Tabs --}}
    <div class="flex flex-wrap gap-2">
        <button
            wire:click="$set('statusFilter', '')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === '' ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Semua ({{ $counts['all'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'siap')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'siap' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Siap Dijemput ({{ $counts['siap'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'diantar')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'diantar' ? 'bg-purple-600 text-white shadow-sm shadow-purple-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Sedang Diantar ({{ $counts['diantar'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'selesai')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'selesai' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Selesai ({{ $counts['selesai'] }})
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
                placeholder="Cari kode pesanan, nama, kelas tujuan..."
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

    {{-- Deliveries Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" wire:poll.10s>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode & Waktu</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemesan & Tujuan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Items</th>
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

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $order->user->name ?? '-' }}</div>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $order->ruangan_tujuan ?? 'Ruang' }}
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">
                                        Kls {{ $order->kelas_tujuan ?? '-' }} {{ $order->jurusan_tujuan ? '('.$order->jurusan_tujuan.')' : '' }}
                                    </span>
                                </div>
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
                                    $statusColors = [
                                        'menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'siap' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'diantar' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'dibatalkan' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border capitalize {{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-700' }}">
                                    {{ $order->status }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-1">
                                @if($order->status === 'siap')
                                    <button
                                        wire:click="updateStatus({{ $order->id }}, 'diantar')"
                                        class="inline-flex items-center px-3 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-semibold shadow-xs transition"
                                    >
                                        Antar Sekarang
                                    </button>
                                @elseif($order->status === 'diantar')
                                    <button
                                        wire:click="updateStatus({{ $order->id }}, 'selesai')"
                                        wire:confirm="Konfirmasi bahwa pesanan #{{ $order->kode_pesanan }} sudah sampai ke siswa?"
                                        class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition"
                                    >
                                        Selesai Diantar
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75A1.125 1.125 0 0 0 13.125 2.625H3.375A1.125 1.125 0 0 0 2.25 3.75v10.5c0 .621.504 1.125 1.125 1.125H5.25m9-7.5H2.25" />
                                    </svg>
                                </div>
                                <p class="text-base font-bold text-gray-900">Tidak ada data pengantaran</p>
                                <p class="text-xs text-gray-400 mt-1">Coba ubah kata kunci pencarian atau reset filter.</p>
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

                    <div class="p-6 space-y-5">
                        <div class="bg-brand-50/60 p-4 rounded-xl border border-brand-100 space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">Nama Pemesan:</span>
                                <span class="font-bold text-gray-900">{{ $selectedOrder->user->name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">Tujuan Pengantaran:</span>
                                <span class="font-bold text-brand-700">
                                    {{ $selectedOrder->ruangan_tujuan ? $selectedOrder->ruangan_tujuan . ' (Kls ' . $selectedOrder->kelas_tujuan . ' ' . $selectedOrder->jurusan_tujuan . ')' : 'Kelas ' . ($selectedOrder->kelas_tujuan ?? '-') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">Pembayaran:</span>
                                <span class="font-bold text-gray-900 uppercase">{{ $selectedOrder->metode_pembayaran }} ({{ $selectedOrder->status_pembayaran === 'sudah_dibayar' ? 'Lunas' : 'Belum Lunas' }})</span>
                            </div>
                            @if($selectedOrder->catatan)
                                <div class="pt-2 border-t border-brand-100 text-xs text-gray-600">
                                    <strong>Catatan:</strong> {{ $selectedOrder->catatan }}
                                </div>
                            @endif
                        </div>

                        <div>
                            <h4 class="text-xs font-semibold uppercase text-gray-400 tracking-wider mb-2">Daftar Item Menu</h4>
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
                                <span>Total Pesanan</span>
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

                        <div class="space-x-2">
                            @if($selectedOrder->status === 'siap')
                                <button
                                    wire:click="updateStatus({{ $selectedOrder->id }}, 'diantar')"
                                    class="px-4 py-2 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700 shadow-xs transition"
                                >
                                    Antar Sekarang
                                </button>
                            @elseif($selectedOrder->status === 'diantar')
                                <button
                                    wire:click="updateStatus({{ $selectedOrder->id }}, 'selesai')"
                                    wire:confirm="Konfirmasi bahwa pesanan #{{ $selectedOrder->kode_pesanan }} sudah sampai ke siswa?"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 shadow-xs transition"
                                >
                                    Selesai Diantar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
