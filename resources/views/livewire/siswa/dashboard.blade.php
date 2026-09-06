<div class="space-y-6">
    {{-- Header Greeting --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-gradient-to-r from-brand-700 via-brand-600 to-brand-800 rounded-2xl p-6 text-white shadow-lg shadow-brand-900/10">
        <div>
            <h1 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}!</h1>
            <p class="text-brand-100 text-sm mt-1">
                @if(auth()->user()->kelas || auth()->user()->jurusan || auth()->user()->ruangan)
                    <span class="font-bold text-white">
                        Kelas {{ auth()->user()->kelas ?? '-' }} {{ auth()->user()->jurusan ? '('.auth()->user()->jurusan.')' : '' }}
                        @if(auth()->user()->ruangan)
                            • {{ auth()->user()->ruangan }}
                        @endif
                    </span>
                    <span class="text-brand-200 ml-1">• SMKN 6 Jember</span>
                @else
                    Siswa SMKN 6 Jember
                @endif
            </p>
        </div>
    </div>

    {{-- Banner Notifikasi Jika Ruangan Belum Dilengkapi --}}
    @if(empty(auth()->user()->ruangan))
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-black text-sm shrink-0">
                    ℹ️
                </div>
                <div>
                    <div class="text-xs font-bold text-blue-950">Lengkapi Ruang Teori / Kelas Anda</div>
                    <div class="text-[11px] text-blue-700 mt-0.5">Atur ruang teori (misal: teori-01) di profil agar kurir dapat mengantar pesanan langsung ke meja kelas Anda secara akurat.</div>
                </div>
            </div>
            <a href="{{ route('profile.show') }}" class="text-xs font-bold text-blue-900 bg-blue-200/80 hover:bg-blue-200 px-3.5 py-1.5 rounded-xl transition shrink-0 self-start sm:self-center">
                Lengkapi Sekarang &rarr;
            </a>
        </div>
    @endif

    {{-- Verification Warning Banner (If Not Verified) --}}
    @if(! auth()->user()->is_verified)
        <div class="p-4 bg-amber-50/80 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-sm shrink-0">
                    !
                </div>
                <div>
                    <div class="text-xs font-bold text-amber-950">
                        {{ auth()->user()->kartu_pelajar_photo ? 'Kartu Pelajar Sedang Diverifikasi Admin' : 'Kartu Pelajar Belum Diverifikasi' }}
                    </div>
                    <div class="text-[11px] text-amber-700 mt-0.5">
                        {{ auth()->user()->kartu_pelajar_photo ? 'Mohon tunggu proses persetujuan oleh admin agar Anda dapat menggunakan metode bayar saldo online.' : 'Verifikasi kartu pelajar Anda terlebih dahulu agar dapat menggunakan saldo untuk pembayaran online.' }}
                    </div>
                </div>
            </div>
            <a href="{{ route('siswa.saldo.index') }}" class="text-xs font-bold text-amber-900 bg-amber-200/80 hover:bg-amber-200 px-3 py-1.5 rounded-xl transition shrink-0 self-start sm:self-center">
                {{ auth()->user()->kartu_pelajar_photo ? 'Lihat Status' : 'Upload Kartu →' }}
            </a>
        </div>
    @endif

    {{-- Metric Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Saldo --}}
        <a href="{{ route('siswa.saldo.index') }}" class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md hover:border-brand-300 transition group block">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                    Rp
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-md bg-emerald-50 text-emerald-800 group-hover:bg-emerald-100 transition">Saldo Saya</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </div>
                <p class="text-xs text-brand-500 mt-1">Klik untuk lihat riwayat &rarr;</p>
            </div>
        </a>

        {{-- Card 2: Pesanan Aktif --}}
        <a href="{{ route('siswa.orders.index') }}" class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md hover:border-brand-300 transition group block">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                @if($pesananAktifCount > 0)
                    <span class="text-xs font-bold px-2 py-1 rounded-md bg-blue-100 text-blue-700 animate-pulse">Sedang Proses</span>
                @else
                    <span class="text-xs font-semibold px-2 py-1 rounded-md bg-gray-100 text-gray-600">Tidak Ada</span>
                @endif
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">{{ $pesananAktifCount }}</div>
                <p class="text-xs text-brand-500 mt-1">Pesanan dalam proses</p>
            </div>
        </a>

        {{-- Card 3: Pesanan Selesai --}}
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-md bg-purple-50 text-purple-800">Selesai</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">{{ $pesananSelesaiCount }}</div>
                <p class="text-xs text-brand-500 mt-1">Total pesanan dinikmati</p>
            </div>
        </div>

        {{-- Card 4: Total Belanja --}}
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div class="size-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-1 rounded-md bg-amber-50 text-amber-800">Pengeluaran</span>
            </div>
            <div class="mt-4">
                <div class="text-2xl font-black text-brand-950">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </div>
                <p class="text-xs text-brand-500 mt-1">Total jajan di cafe</p>
            </div>
        </div>
    </div>

    {{-- Main Grid: Pesanan Aktif (Left 2 cols) + Menu Rekomendasi (Right 1 col) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Status Pesanan Aktif --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-brand-100 p-6 shadow-xs" wire:poll.10s>
            <div class="flex items-center justify-between pb-4 border-b border-brand-100">
                <div>
                    <h2 class="text-lg font-bold text-brand-950 flex items-center gap-2">
                        <span>Pesanan Sedang Diproses</span>
                        @if($pesananAktifCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-brand-600 text-white animate-pulse">
                                {{ $pesananAktifCount }}
                            </span>
                        @endif
                    </h2>
                    <p class="text-xs text-brand-500 mt-0.5">Lacak status pembuatan dan pengantaran pesanan Anda secara real-time</p>
                </div>
                <a href="{{ route('siswa.orders.index') }}" class="text-xs font-semibold text-brand-700 hover:text-brand-900 bg-brand-50 hover:bg-brand-100 px-3 py-1.5 rounded-lg transition">
                    Semua Pesanan &rarr;
                </a>
            </div>

            <div class="divide-y divide-brand-50 mt-2">
                @forelse ($activeOrders as $order)
                    <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="size-11 rounded-xl bg-brand-50 text-brand-700 flex items-center justify-center shrink-0 font-mono text-xs font-bold">
                                #{{ substr($order->kode_pesanan, -4) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-brand-950 truncate flex items-center gap-2">
                                    <span>{{ $order->items->map(fn($i) => ($i->menu->nama ?? 'Menu') . ' (' . $i->jumlah . 'x)')->join(', ') }}</span>
                                </div>
                                <div class="text-xs text-brand-500 truncate mt-0.5 flex items-center gap-2">
                                    <span>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                    <span>•</span>
                                    <span>{{ $order->tipe_pengiriman === 'antar' ? 'Diantar ke ' . ($order->ruangan_tujuan ?? 'Ruang') . ' (' . ($order->jurusan_tujuan ?? 'Kls '.$order->kelas_tujuan) . ')' : 'Ambil Sendiri' }}</span>
                                    @if($order->kurir)
                                        <span>•</span>
                                        <span class="text-blue-600 font-medium">Kurir: {{ $order->kurir->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 self-end sm:self-center shrink-0">
                            @php
                                $statusBadges = [
                                    'menunggu' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'siap' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'diantar' => 'bg-purple-50 text-purple-700 border-purple-200',
                                ];
                                $statusLabels = [
                                    'menunggu' => 'Menunggu Konfirmasi',
                                    'diproses' => 'Sedang Dimasak',
                                    'siap' => 'Siap / Tunggu Kurir',
                                    'diantar' => 'Kurir Sedang Antar',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusBadges[$order->status] ?? 'bg-gray-50 text-gray-700' }}">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>
                            <a href="{{ route('siswa.orders.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">
                                Detail &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <div class="size-12 rounded-full bg-brand-50 text-brand-400 mx-auto flex items-center justify-center mb-3">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-900">Belum ada pesanan aktif</p>
                        <p class="text-xs text-gray-400 mt-1 mb-3">Kamu belum membuat pesanan yang sedang berjalan.</p>
                        <a href="{{ route('siswa.menu.index') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-xs font-bold rounded-xl hover:bg-brand-700 shadow-sm shadow-brand-500/30 transition">
                            Pesan Menu Sekarang &rarr;
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right: Menu Pilihan Hari Ini --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-brand-100 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-brand-950">Menu Tersedia</h3>
                    <a href="{{ route('siswa.menu.index') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-800">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($featuredMenus as $menu)
                        <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-brand-50/60 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                @if($menu->gambar)
                                    <img src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama }}" class="size-12 rounded-xl object-cover shrink-0 border border-brand-100">
                                @else
                                    <div class="size-12 rounded-xl bg-brand-50 text-brand-400 flex items-center justify-center shrink-0 font-bold text-xs">
                                        {{ substr($menu->nama, 0, 2) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-brand-950 truncate">{{ $menu->nama }}</div>
                                    <div class="text-xs text-brand-600 font-black mt-0.5">Rp {{ number_format($menu->harga, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <a href="{{ route('siswa.menu.index') }}" class="px-2.5 py-1 text-xs font-bold rounded-lg bg-brand-600 hover:bg-brand-700 text-white shrink-0 transition">
                                Pesan
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-4">Belum ada menu tersedia.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
