<div class="space-y-6">
    <!-- Header Greeting -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-brand-700 via-brand-600 to-brand-800 rounded-2xl p-6 text-white shadow-lg shadow-brand-900/10">
        <div>
            <h1 class="text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-brand-100 text-sm mt-1">Pantau performa cafe, pesanan masuk, dan operasional hari ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-white/15 text-white backdrop-blur-xs border border-white/20">
                <span class="size-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </span>
        </div>
    </div>

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Pendapatan -->
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold">
                    Rp
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-md bg-amber-50 text-amber-800">Hari Ini</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">
                    Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}
                </div>
                <p class="text-xs text-brand-500 mt-1">Pendapatan kotor pesanan aktif</p>
            </div>
        </div>

        <!-- Card 2: Pesanan Perlu Proses -->
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                @if ($pesananPerluProses > 0)
                    <span class="text-xs font-bold px-2 py-1 rounded-md bg-red-100 text-red-700 animate-pulse">Perlu Respon</span>
                @else
                    <span class="text-xs font-semibold px-2 py-1 rounded-md bg-gray-100 text-gray-600">Aman</span>
                @endif
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">{{ $pesananPerluProses }}</div>
                <p class="text-xs text-brand-500 mt-1">Pesanan menunggu & diproses</p>
            </div>
        </div>

        <!-- Card 3: Total Pesanan Hari Ini -->
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-md bg-brand-50 text-brand-800">Semua Status</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">{{ $pesananHariIni }}</div>
                <p class="text-xs text-brand-500 mt-1">Total pesanan masuk hari ini</p>
            </div>
        </div>

        <!-- Card 4: Menu Aktif & Kurir -->
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-md bg-emerald-50 text-emerald-800">{{ $totalKurir }} Kurir</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">{{ $totalMenuAktif }} Menu</div>
                <p class="text-xs text-brand-500 mt-1">{{ $totalSiswa }} siswa terdaftar</p>
            </div>
        </div>
    </div>

    <!-- Main Grid: Recent Orders + Quick Actions / Mini Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Recent Orders (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-brand-100 p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-brand-100">
<div wire:poll.5s>
                    <h2 class="text-lg font-bold text-brand-950">Pesanan Masuk Terbaru</h2>
                    <p class="text-xs text-brand-500 mt-0.5">Daftar transaksi pesanan terkini yang masuk sistem</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-900 bg-brand-50 hover:bg-brand-100 px-3 py-1.5 rounded-lg transition">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="divide-y divide-brand-50 mt-2">
                @forelse ($pesananTerbaru as $order)
                    <div class="py-3.5 flex items-center justify-between gap-4">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="size-10 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center shrink-0 font-mono text-xs font-bold">
                                #{{ substr($order->kode_pesanan, -4) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-brand-950 truncate flex items-center gap-2">
                                    <span>{{ $order->user->name ?? 'Siswa' }}</span>
                                    @if ($order->tipe_pengiriman === 'antar')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            Antar: Kls {{ $order->kelas_tujuan ?? '-' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                            Ambil Sendiri
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-brand-500 truncate mt-0.5">
                                    {{ $order->items->count() }} item • Rp {{ number_format($order->total_harga, 0, ',', '.') }} • {{ $order->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2 shrink-0">
                            @php
                                $statusClasses = match($order->status) {
                                    'menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'siap' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'diantar' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-gray-50 text-gray-700 border-gray-200'
                                };
                            @endphp
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full border capitalize {{ $statusClasses }}">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <div class="size-12 rounded-full bg-brand-50 text-brand-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-brand-900">Belum ada pesanan masuk</p>
                        <p class="text-xs text-brand-400 mt-1">Pesanan dari siswa akan muncul di sini secara real-time.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Quick Actions & Performance Summary (1 col) -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl border border-brand-100 p-6 shadow-xs">
                <h3 class="text-sm font-bold text-brand-950 mb-3">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="{{ route('admin.menu.index') }}" class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-brand-100 hover:border-brand-300 hover:bg-brand-50 transition text-center group">
                        <div class="size-9 rounded-lg bg-brand-100 group-hover:bg-brand-600 text-brand-700 group-hover:text-white flex items-center justify-center mb-2 transition">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-brand-900">Tambah Menu</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-brand-100 hover:border-brand-300 hover:bg-brand-50 transition text-center group">
                        <div class="size-9 rounded-lg bg-brand-100 group-hover:bg-brand-600 text-brand-700 group-hover:text-white flex items-center justify-center mb-2 transition">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-brand-900">Kelola Pesanan</span>
                    </a>

                    <a href="{{ route('admin.verifikasi.index') }}" class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-brand-100 hover:border-brand-300 hover:bg-brand-50 transition text-center group">
                        <div class="size-9 rounded-lg bg-brand-100 group-hover:bg-brand-600 text-brand-700 group-hover:text-white flex items-center justify-center mb-2 transition">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-brand-900">Verifikasi Kartu</span>
                    </a>

                    <a href="{{ route('admin.kurir.index') }}" class="flex flex-col items-center justify-center p-3.5 rounded-xl border border-brand-100 hover:border-brand-300 hover:bg-brand-50 transition text-center group">
                        <div class="size-9 rounded-lg bg-brand-100 group-hover:bg-brand-600 text-brand-700 group-hover:text-white flex items-center justify-center mb-2 transition">
                            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-brand-900">Status Kurir</span>
                    </a>
                </div>
            </div>

            <!-- Mini Daily Trend (Last 7 Days) -->
            <div class="bg-white rounded-2xl border border-brand-100 p-6 shadow-xs">
                <h3 class="text-sm font-bold text-brand-950 mb-1">Aktivitas 7 Hari Terakhir</h3>
                <p class="text-xs text-brand-500 mb-4">Grafik total pesanan selesai</p>
                
                <div class="flex items-end justify-between gap-2 h-32 pt-4">
                    @php
                        $maxTotal = max(array_column($salesTrend, 'total') ?: [1]);
                        $maxTotal = $maxTotal > 0 ? $maxTotal : 1;
                    @endphp
                    @foreach ($salesTrend as $item)
                        @php
                            $heightPercent = max(8, round(($item['total'] / $maxTotal) * 100));
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
</div>
