<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Manajemen Pesanan') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Kelola dan pantau semua pesanan yang masuk.') }}
            </p>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="mb-6 flex flex-wrap gap-2">
        <button
            wire:click="$set('statusFilter', '')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === '' ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Semua ({{ $counts['all'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'menunggu')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'menunggu' ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Menunggu ({{ $counts['menunggu'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'diproses')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'diproses' ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Diproses ({{ $counts['diproses'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'siap')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'siap' ? 'bg-indigo-500 text-white shadow-sm shadow-indigo-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Siap ({{ $counts['siap'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'diantar')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'diantar' ? 'bg-purple-500 text-white shadow-sm shadow-purple-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Diantar ({{ $counts['diantar'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'selesai')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'selesai' ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Selesai ({{ $counts['selesai'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'dibatalkan')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'dibatalkan' ? 'bg-rose-500 text-white shadow-sm shadow-rose-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Dibatalkan ({{ $counts['dibatalkan'] }})
        </button>
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6 flex flex-col lg:flex-row gap-4 justify-between items-center">
        <div class="w-full lg:w-96 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari kode pesanan, nama siswa..."
                class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
            />
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <select
                wire:model.live="pengirimanFilter"
                class="border border-gray-200 rounded-lg text-sm py-2 px-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white"
            >
                <option value="">Semua Pengiriman</option>
                <option value="antar">Diantar</option>
                <option value="ambil">Ambil Sendiri</option>
            </select>

            <select
                wire:model.live="pembayaranFilter"
                class="border border-gray-200 rounded-lg text-sm py-2 px-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white"
            >
                <option value="">Semua Pembayaran</option>
                <option value="saldo">Saldo</option>
                <option value="cod">COD</option>
            </select>

            <input
                type="date"
                wire:model.live="dateFilter"
                class="border border-gray-200 rounded-lg text-sm py-2 px-3 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white"
            />

            @if($search || $statusFilter || $pengirimanFilter || $pembayaranFilter || $dateFilter)
                <button
                    wire:click="resetFilter"
                    class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2.5 py-1 rounded-lg hover:bg-rose-50 border border-rose-200 transition"
                >
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" wire:poll.5s>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pesanan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pemesan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengiriman</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 text-sm font-mono">#{{ $order->kode_pesanan }}</div>
                                <div class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900 text-sm">{{ $order->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-400">
                                    {{ $order->user->username ? '@'.$order->user->username : '' }}
                                    @if($order->user->kelas)
                                        • Kelas {{ $order->user->kelas }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-800 line-clamp-1 max-w-xs">
                                    {{ $order->items->map(fn($item) => $item->menu->nama . ' (' . $item->jumlah . 'x)')->join(', ') }}
                                </div>
                                <div class="text-xs text-gray-400">{{ $order->items->count() }} item berbeda</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 text-sm">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $order->metode_pembayaran === 'saldo' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ strtoupper($order->metode_pembayaran) }}
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $order->status_pembayaran === 'sudah_dibayar' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $order->status_pembayaran === 'sudah_dibayar' ? 'Lunas' : 'Belum' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $order->tipe_pengiriman === 'antar' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $order->tipe_pengiriman === 'antar' ? 'Diantar' : 'Ambil Sendiri' }}
                                    </span>
                                </div>
                                @if($order->tipe_pengiriman === 'antar')
                                    <div class="text-[11px] font-bold text-gray-800 mt-1">
                                        {{ $order->ruangan_tujuan ? $order->ruangan_tujuan : 'Kls '.$order->kelas_tujuan }} 
                                        {{ $order->jurusan_tujuan ? '('.$order->jurusan_tujuan.')' : '' }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        @if($order->kurir)
                                            Kurir: <span class="font-medium text-gray-700">{{ $order->kurir->name }}</span>
                                        @else
                                            <button wire:click="openAssignKurir({{ $order->id }})" class="text-brand-600 hover:text-brand-800 font-medium text-[11px] underline">
                                                + Tugaskan Kurir
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'menunggu' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                        'diproses' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                        'siap' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
                                        'diantar' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                                        'selesai' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                        'dibatalkan' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $statusClasses[$order->status] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button wire:click="openDetail({{ $order->id }})" class="text-brand-600 hover:text-brand-900 bg-brand-50 hover:bg-brand-100 px-2.5 py-1 rounded-lg text-xs font-semibold transition">
                                    Detail
                                </button>

                                {{-- Quick Status Action --}}
                                @if($order->status === 'menunggu')
                                    <button wire:click="updateStatus({{ $order->id }}, 'diproses')" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg text-xs font-semibold transition">
                                        Proses
                                    </button>
                                @elseif($order->status === 'diproses')
                                    <button wire:click="updateStatus({{ $order->id }}, 'siap')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg text-xs font-semibold transition">
                                        Siap
                                    </button>
                                @elseif($order->status === 'siap' && $order->tipe_pengiriman === 'ambil')
                                    <button wire:click="updateStatus({{ $order->id }}, 'selesai')" class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg text-xs font-semibold transition">
                                        Selesai
                                    </button>
                                @elseif($order->status === 'siap' && $order->tipe_pengiriman === 'antar')
                                    <button wire:click="updateStatus({{ $order->id }}, 'diantar')" class="text-purple-600 hover:text-purple-900 bg-purple-50 hover:bg-purple-100 px-2.5 py-1 rounded-lg text-xs font-semibold transition">
                                        Diantar
                                    </button>
                                @elseif($order->status === 'diantar')
                                    <button wire:click="updateStatus({{ $order->id }}, 'selesai')" class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg text-xs font-semibold transition">
                                        Selesai
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                Belum ada pesanan yang sesuai filter.
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

    {{-- Modal Detail Pesanan --}}
    @if($showDetailModal && $selectedOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeDetail"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    {{-- Header --}}
                    <div class="bg-brand-900 px-6 py-5 text-white flex justify-between items-center">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-xl font-bold font-mono">#{{ $selectedOrder->kode_pesanan }}</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClasses[$selectedOrder->status] ?? 'bg-white text-gray-800' }}">
                                    {{ ucfirst($selectedOrder->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-brand-200 mt-1">{{ $selectedOrder->created_at->isoFormat('dddd, D MMMM Y - HH:mm') }}</p>
                        </div>
                        <button wire:click="closeDetail" class="text-brand-300 hover:text-white transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Info Pelanggan & Pengiriman --}}
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl text-sm">
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase">Pemesan</div>
                                <div class="font-bold text-gray-900 mt-0.5">{{ $selectedOrder->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $selectedOrder->user->email }}</div>
                                @if($selectedOrder->user->kelas)
                                    <div class="text-xs text-gray-500">Kelas: {{ $selectedOrder->user->kelas }}</div>
                                @endif
                            </div>
                            <div>
                                <div class="text-xs text-gray-400 font-semibold uppercase">Pengiriman & Lokasi Antar</div>
                                <div class="font-bold text-gray-900 mt-0.5">
                                    {{ $selectedOrder->tipe_pengiriman === 'antar' ? 'Diantar Kurir' : 'Ambil Sendiri di Kantin' }}
                                </div>
                                @if($selectedOrder->tipe_pengiriman === 'antar')
                                    <div class="text-xs text-brand-700 font-bold mt-1 bg-brand-50 p-2 rounded-lg border border-brand-200">
                                        Kelas {{ $selectedOrder->kelas_tujuan ?? '-' }} {{ $selectedOrder->jurusan_tujuan ? '('.$selectedOrder->jurusan_tujuan.')' : '' }}
                                        <div class="text-gray-600 font-semibold text-[11px] mt-0.5">
                                            Ruangan: <span class="text-brand-900 font-black">{{ $selectedOrder->ruangan_tujuan ?? '-' }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if($selectedOrder->kurir)
                                    <div class="text-xs text-blue-600 font-medium mt-1">Kurir: {{ $selectedOrder->kurir->name }}</div>
                                @endif
                            </div>
                        </div>

                        {{-- Items Pesanan --}}
                        <div>
                            <h4 class="text-xs font-semibold uppercase text-gray-400 tracking-wider mb-3">Item Pesanan</h4>
                            <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden">
                                @foreach($selectedOrder->items as $item)
                                    <div class="p-3 bg-white flex justify-between items-center text-sm">
                                        <div class="flex items-center gap-3">
                                            @if($item->menu && $item->menu->gambar)
                                                <img src="{{ Storage::url($item->menu->gambar) }}" class="size-10 rounded-lg object-cover">
                                            @else
                                                <div class="size-10 rounded-lg bg-brand-100 flex items-center justify-center text-brand-600 font-bold text-xs">
                                                    {{ substr($item->menu->nama ?? 'M', 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $item->menu->nama ?? 'Menu Dihapus' }}</div>
                                                <div class="text-xs text-gray-400">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }} × {{ $item->jumlah }}</div>
                                            </div>
                                        </div>
                                        <div class="font-bold text-gray-900">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                                <div class="p-3 bg-gray-50 flex justify-between items-center font-bold text-base">
                                    <span>Total Pembayaran</span>
                                    <span class="text-brand-600">Rp {{ number_format($selectedOrder->total_harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Catatan --}}
                        @if($selectedOrder->catatan)
                            <div class="bg-amber-50 p-3 rounded-xl border border-amber-200">
                                <div class="text-xs font-semibold text-amber-800 uppercase">Catatan Pemesan</div>
                                <div class="text-sm text-amber-900 mt-1">{{ $selectedOrder->catatan }}</div>
                            </div>
                        @endif

                        {{-- Status Flow Actions --}}
                        <div>
                            <h4 class="text-xs font-semibold uppercase text-gray-400 tracking-wider mb-3">Ubah Status Pesanan</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['menunggu', 'diproses', 'siap', 'diantar', 'selesai', 'dibatalkan'] as $st)
                                    <button
                                        wire:click="updateStatus({{ $selectedOrder->id }}, '{{ $st }}')"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $selectedOrder->status === $st ? 'bg-brand-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                    >
                                        {{ ucfirst($st) }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-100">
                        @if($selectedOrder->tipe_pengiriman === 'antar')
                            <button
                                wire:click="openAssignKurir({{ $selectedOrder->id }})"
                                class="text-xs font-semibold text-brand-600 hover:text-brand-800"
                            >
                                {{ $selectedOrder->kurir ? 'Ganti Kurir' : '+ Tugaskan Kurir' }}
                            </button>
                        @else
                            <div></div>
                        @endif
                        <button
                            wire:click="closeDetail"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold hover:bg-gray-300 transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Assign Kurir --}}
    @if($showAssignKurirModal && $selectedOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeAssignKurir"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Tugaskan Kurir</h3>
                        <p class="text-xs text-gray-500 mb-4">Pilih kurir untuk mengantar pesanan #{{ $selectedOrder->kode_pesanan }} (Tujuan: {{ $selectedOrder->kelas_tujuan ?? '-' }})</p>

                        <div class="space-y-2 max-h-60 overflow-y-auto mb-6">
                            @forelse($kurirs as $kurir)
                                <label class="flex items-center p-3 border rounded-xl cursor-pointer hover:bg-brand-50 transition {{ $selectedKurirId == $kurir->id ? 'border-brand-500 bg-brand-50/50' : 'border-gray-200' }}">
                                    <input
                                        type="radio"
                                        wire:model="selectedKurirId"
                                        value="{{ $kurir->id }}"
                                        class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300"
                                    />
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-gray-900">{{ $kurir->name }}</div>
                                        <div class="text-xs text-gray-400">{{ '@'.$kurir->username }}</div>
                                    </div>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-4">Belum ada kurir aktif.</p>
                            @endforelse
                        </div>

                        <div class="flex justify-end gap-3">
                            <button wire:click="closeAssignKurir" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-50">
                                Batal
                            </button>
                            <button wire:click="assignKurir" class="px-4 py-2 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700 shadow-sm shadow-brand-500/30">
                                Simpan Kurir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
