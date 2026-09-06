<div class="space-y-6">
    {{-- Header Greeting --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-brand-700 via-brand-600 to-brand-800 rounded-2xl p-6 text-white shadow-lg shadow-brand-900/10">
        <div>
            <h1 class="text-2xl font-bold">Selamat Bertugas, {{ auth()->user()->name }}!</h1>
            <p class="text-brand-100 text-sm mt-1">Pantau dan antarkan pesanan siswa ke kelas dengan cepat dan aman.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-white/15 text-white backdrop-blur-xs border border-white/20">
                <span class="size-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    {{-- Metric Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Pengantaran Aktif --}}
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center font-bold">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75A1.125 1.125 0 0 0 13.125 2.625H3.375A1.125 1.125 0 0 0 2.25 3.75v10.5c0 .621.504 1.125 1.125 1.125H5.25m9-7.5H2.25" />
                    </svg>
                </div>
                @if($pengantaranAktif > 0)
                    <span class="text-xs font-bold px-2 py-1 rounded-md bg-blue-100 text-blue-700 animate-pulse">Perlu Aksi</span>
                @else
                    <span class="text-xs font-semibold px-2 py-1 rounded-md bg-gray-100 text-gray-600">Aman</span>
                @endif
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">{{ $pengantaranAktif }}</div>
                <p class="text-xs text-brand-500 mt-1">Siap & sedang diantar</p>
            </div>
        </div>

        {{-- Card 2: Selesai Hari Ini --}}
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-md bg-emerald-50 text-emerald-800">Hari Ini</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">{{ $selesaiHariIni }}</div>
                <p class="text-xs text-brand-500 mt-1">Pesanan sukses diantar</p>
            </div>
        </div>

        {{-- Card 3: Total Pengantaran --}}
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-md bg-amber-50 text-amber-800">Total</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">{{ $totalPengantaran }}</div>
                <p class="text-xs text-brand-500 mt-1">Semua riwayat tugas</p>
            </div>
        </div>

        {{-- Card 4: Tingkat Keberhasilan --}}
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                    %
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-md bg-purple-50 text-purple-800">Performa</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">{{ $tingkatKeberhasilan }}%</div>
                <p class="text-xs text-brand-500 mt-1">Tingkat penyelesaian</p>
            </div>
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

    {{-- Main Grid: Active Deliveries (Left 2 cols) + Actions & Trend (Right 1 col) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Tugas Aktif Saat Ini --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-brand-100 p-6 shadow-xs" wire:poll.10s>
            <div class="flex items-center justify-between pb-4 border-b border-brand-100">
                <div>
                    <h2 class="text-lg font-bold text-brand-950 flex items-center gap-2">
                        <span>Tugas Pengantaran Aktif</span>
                        @if($pengantaranAktif > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-600 text-white animate-pulse">
                                {{ $pengantaranAktif }}
                            </span>
                        @endif
                    </h2>
                    <p class="text-xs text-brand-500 mt-0.5">Pesanan yang perlu diambil di kantin atau sedang diantar</p>
                </div>
                <a href="{{ route('kurir.deliveries.index') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-900 bg-brand-50 hover:bg-brand-100 px-3 py-1.5 rounded-lg transition">
                    Semua Tugas &rarr;
                </a>
            </div>

            <div class="divide-y divide-brand-50 mt-2">
                @forelse ($tugasAktif as $order)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="size-11 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center shrink-0 font-mono text-xs font-bold">
                                #{{ substr($order->kode_pesanan, -4) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-brand-950 truncate flex items-center gap-1.5 flex-wrap">
                                    <span>{{ $order->user->name ?? 'Siswa' }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $order->ruangan_tujuan ?? 'Ruang' }} (Kls {{ $order->kelas_tujuan ?? '-' }} {{ $order->jurusan_tujuan ? $order->jurusan_tujuan : '' }})
                                    </span>
                                </div>
                                <div class="text-xs text-brand-500 truncate mt-0.5">
                                    {{ $order->items->count() }} item • Rp {{ number_format($order->total_harga, 0, ',', '.') }} • {{ $order->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                            @if($order->status === 'siap')
                                <button
                                    wire:click="updateStatus({{ $order->id }}, 'diantar')"
                                    class="px-3.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg text-xs font-semibold shadow-xs transition"
                                >
                                    Antar Sekarang
                                </button>
                            @elseif($order->status === 'diantar')
                                <button
                                    wire:click="updateStatus({{ $order->id }}, 'selesai')"
                                    wire:confirm="Konfirmasi bahwa pesanan #{{ $order->kode_pesanan }} sudah sampai ke siswa?"
                                    class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition"
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
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <div class="size-12 rounded-full bg-brand-50 text-brand-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75A1.125 1.125 0 0 0 13.125 2.625H3.375A1.125 1.125 0 0 0 2.25 3.75v10.5c0 .621.504 1.125 1.125 1.125H5.25m9-7.5H2.25" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-900">Tidak ada tugas aktif saat ini</p>
                        <p class="text-xs text-gray-400 mt-1">Saat admin menugaskan pesanan siap antar, otomatis muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Quick Actions + Weekly Activity --}}
        <div class="space-y-6">
            {{-- Quick Action Card --}}
            <div class="bg-white rounded-2xl border border-brand-100 p-6 shadow-xs">
                <h3 class="text-sm font-bold text-brand-950 mb-3">Menu Cepat</h3>
                <div class="grid grid-cols-1 gap-2.5">
                    <a href="{{ route('kurir.deliveries.index') }}" class="flex items-center p-3.5 rounded-xl border border-brand-100 hover:border-brand-300 hover:bg-brand-50 transition group">
                        <div class="size-10 rounded-xl bg-brand-100 group-hover:bg-brand-600 text-brand-700 group-hover:text-white flex items-center justify-center mr-3 transition shrink-0">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75A1.125 1.125 0 0 0 13.125 2.625H3.375A1.125 1.125 0 0 0 2.25 3.75v10.5c0 .621.504 1.125 1.125 1.125H5.25m9-7.5H2.25" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-brand-900">Semua Daftar Pengantaran</div>
                            <div class="text-[11px] text-brand-500 mt-0.5">Lihat riwayat lengkap dan filter tugas</div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- 7 Days Activity Mini Chart --}}
            <div class="bg-white rounded-2xl border border-brand-100 p-6 shadow-xs">
                <h3 class="text-sm font-bold text-brand-950 mb-1">Aktivitas 7 Hari Terakhir</h3>
                <p class="text-xs text-brand-500 mb-4">Total pesanan berhasil Anda antarkan</p>

                <div class="flex items-end justify-between gap-2 h-32 pt-4">
                    @php
                        $maxCount = max(array_column($weeklyTrend, 'count') ?: [1]);
                        $maxCount = $maxCount > 0 ? $maxCount : 1;
                    @endphp
                    @foreach ($weeklyTrend as $item)
                        @php
                            $heightPercent = max(8, round(($item['count'] / $maxCount) * 100));
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group">
                            <div class="text-[10px] font-bold text-brand-800 opacity-0 group-hover:opacity-100 transition mb-1 truncate">
                                {{ $item['count'] }}
                            </div>
                            <div class="w-full bg-brand-100 group-hover:bg-brand-600 rounded-t-md transition-all duration-300" style="height: {{ $heightPercent }}%;"></div>
                            <span class="text-[10px] text-brand-500 mt-2 font-medium">{{ $item['day'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
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
