<div class="space-y-6 pb-24">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Pesan Menu Cafe') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Pilih menu makanan dan minuman favoritmu, lalu bayar dengan saldo atau COD.') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                Saldo Anda: <strong class="ml-1 text-emerald-900">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</strong>
            </span>
        </div>
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

    {{-- Category Pills & Search --}}
    <div class="space-y-4">
        {{-- Category Pills --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
            <button
                wire:click="$set('kategori', '')"
                class="px-4 py-2 text-xs font-bold rounded-xl whitespace-nowrap transition {{ $kategori === '' ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/30' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}"
            >
                Semua Menu
            </button>
            @foreach($categories as $cat)
                <button
                    wire:click="$set('kategori', '{{ $cat->id }}')"
                    class="px-4 py-2 text-xs font-bold rounded-xl whitespace-nowrap transition flex items-center gap-1.5 {{ $kategori == $cat->id ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/30' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}"
                >
                    @if($cat->gambar)
                        <img src="{{ asset('storage/' . $cat->gambar) }}" class="size-4 object-cover rounded-md inline">
                    @elseif($cat->icon)
                        <span>
                            @switch($cat->icon)
                                @case('utensils') 🍴 @break
                                @case('cup-hot') ☕ @break
                                @case('wine') 🥤 @break
                                @case('cookie') 🍪 @break
                                @case('cake') 🍰 @break
                                @case('flame') 🔥 @break
                                @case('burger') 🍔 @break
                                @case('ice-cream') 🍨 @break
                                @case('sparkles') ✨ @break
                                @default 🏷️
                            @endswitch
                        </span>
                    @endif
                    <span>{{ $cat->nama }}</span>
                </button>
            @endforeach
        </div>

        {{-- Search Bar --}}
        <div class="bg-white p-3 rounded-2xl border border-gray-200 shadow-sm flex items-center">
            <div class="pl-2 pr-3 text-gray-400">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari makanan, minuman, cemilan..."
                class="block w-full border-0 focus:ring-0 text-sm placeholder-gray-400 p-0 text-gray-900"
            />
            @if($search || $kategori)
                <button
                    wire:click="$set('search', ''); $set('kategori', '');"
                    class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-3 py-1 rounded-lg hover:bg-rose-50 transition shrink-0"
                >
                    Reset
                </button>
            @endif
        </div>
    </div>

    {{-- Menu Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($menus as $menu)
            <div class="bg-white rounded-2xl border border-brand-100 shadow-xs overflow-hidden flex flex-col group hover:shadow-md transition">
                {{-- Image Container --}}
                <div class="relative h-44 bg-brand-50 overflow-hidden">
                    @if ($menu->gambar)
                        <img src="{{ asset('storage/' . $menu->gambar) }}" alt="{{ $menu->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-brand-300 bg-brand-50/70">
                            <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                            </svg>
                            <span class="text-xs font-medium mt-1">Tanpa Gambar</span>
                        </div>
                    @endif

                    <span class="absolute top-3 left-3 px-2.5 py-1 text-[11px] font-bold rounded-lg bg-white/90 text-brand-900 backdrop-blur-xs shadow-xs">
                        {{ $menu->kategori->nama ?? 'Umum' }}
                    </span>
                </div>

                {{-- Info Body --}}
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-brand-950 line-clamp-1" title="{{ $menu->nama }}">
                            {{ $menu->nama }}
                        </h3>
                        <p class="text-xs text-brand-500 mt-1 line-clamp-2 min-h-8">
                            {{ $menu->deskripsi ?: 'Menu lezat siap disantap.' }}
                        </p>
                    </div>

                    <div class="pt-3 mt-3 border-t border-brand-50 flex items-center justify-between">
                        <div>
                            <div class="text-base font-black text-brand-900">
                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Add to Cart / Qty Control --}}
                        <div>
                            @if(isset($cart[$menu->id]))
                                <div class="flex items-center gap-1.5 bg-brand-50 rounded-xl p-1 border border-brand-200">
                                    <button
                                        wire:click="updateQty({{ $menu->id }}, {{ $cart[$menu->id]['qty'] - 1 }})"
                                        class="size-7 rounded-lg bg-white text-brand-800 font-black text-sm flex items-center justify-center hover:bg-brand-600 hover:text-white transition shadow-2xs"
                                    >
                                        -
                                    </button>
                                    <span class="text-xs font-bold text-brand-950 px-1 min-w-4 text-center">
                                        {{ $cart[$menu->id]['qty'] }}
                                    </span>
                                    <button
                                        wire:click="updateQty({{ $menu->id }}, {{ $cart[$menu->id]['qty'] + 1 }})"
                                        class="size-7 rounded-lg bg-white text-brand-800 font-black text-sm flex items-center justify-center hover:bg-brand-600 hover:text-white transition shadow-2xs"
                                    >
                                        +
                                    </button>
                                </div>
                            @else
                                <button
                                    wire:click="addToCart({{ $menu->id }})"
                                    class="px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-xs transition flex items-center gap-1"
                                >
                                    <span>+ Tambah</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-brand-100">
                <div class="size-12 rounded-full bg-brand-50 text-brand-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <p class="text-sm font-bold text-brand-900">Tidak ada menu yang tersedia</p>
                <p class="text-xs text-brand-400 mt-1">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
            </div>
        @endforelse
    </div>

    {{-- Floating Cart Bottom Bar (Sticky) --}}
    @if($cartCount > 0)
        <div class="fixed bottom-5 left-0 right-0 z-40 px-4 sm:px-6 lg:pl-68 lg:pr-6">
            <div class="max-w-3xl mx-auto bg-brand-900/95 backdrop-blur-md text-white rounded-2xl p-3.5 sm:p-4 shadow-xl shadow-brand-950/20 flex items-center justify-between gap-4 border border-white/10">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="size-10 rounded-xl bg-white/15 flex items-center justify-center font-black text-sm shrink-0 border border-white/10">
                        {{ $cartCount }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] text-brand-200 truncate">Total ({{ $cartCount }} item)</div>
                        <div class="text-base sm:text-lg font-black text-white truncate">Rp {{ number_format($cartTotal, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button
                        wire:click="clearCart"
                        wire:confirm="Kosongkan keranjang belanja?"
                        class="px-3 py-2 text-xs font-semibold text-brand-300 hover:text-white rounded-xl hover:bg-white/10 transition"
                    >
                        Batal
                    </button>
                    <button
                        wire:click="openCheckout"
                        class="px-4 sm:px-5 py-2.5 bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-1.5"
                    >
                        <span>Checkout</span>
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Checkout Modal --}}
    @if($showCheckoutModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeCheckout"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-brand-900 px-6 py-5 text-white flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Konfirmasi Pemesanan</h3>
                            <p class="text-xs text-brand-200 mt-0.5">Periksa kembali pesanan dan rincian pengantaran</p>
                        </div>
                        <button wire:click="closeCheckout" class="text-brand-300 hover:text-white transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="checkout">
                        <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                            {{-- Ringkasan Items --}}
                            <div>
                                <h4 class="text-xs font-semibold uppercase text-gray-400 tracking-wider mb-2">Item yang Dipesan</h4>
                                <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden max-h-48 overflow-y-auto">
                                    @foreach($cart as $id => $item)
                                        <div class="p-3 bg-white flex justify-between items-center text-sm">
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $item['nama'] }}</div>
                                                <div class="text-xs text-gray-400">Rp {{ number_format($item['harga'], 0, ',', '.') }} × {{ $item['qty'] }}</div>
                                            </div>
                                            <div class="font-bold text-gray-900 text-sm">
                                                Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="flex justify-between items-center pt-3 px-1 text-sm font-black text-gray-900">
                                    <span>Total Pembayaran</span>
                                    <span class="text-brand-600 text-base">Rp {{ number_format($cartTotal, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            {{-- Pilihan Tipe Pengiriman --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-2">Metode Pengambilan</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer border rounded-xl p-3 flex flex-col items-center text-center transition {{ $tipe_pengiriman === 'antar' ? 'border-brand-600 bg-brand-50/60 ring-1 ring-brand-600' : 'border-gray-200 hover:bg-gray-50' }}">
                                        <input type="radio" wire:model.live="tipe_pengiriman" value="antar" class="sr-only" />
                                        <svg class="size-6 text-brand-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75A1.125 1.125 0 0 0 13.125 2.625H3.375A1.125 1.125 0 0 0 2.25 3.75v10.5c0 .621.504 1.125 1.125 1.125H5.25m9-7.5H2.25" />
                                        </svg>
                                        <span class="text-xs font-bold text-gray-900">Diantar Kurir</span>
                                        <span class="text-[10px] text-gray-500">Diantar langsung ke kelas</span>
                                    </label>

                                    <label class="cursor-pointer border rounded-xl p-3 flex flex-col items-center text-center transition {{ $tipe_pengiriman === 'ambil' ? 'border-brand-600 bg-brand-50/60 ring-1 ring-brand-600' : 'border-gray-200 hover:bg-gray-50' }}">
                                        <input type="radio" wire:model.live="tipe_pengiriman" value="ambil" class="sr-only" />
                                        <svg class="size-6 text-brand-600 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72m-13.5 8.651h3m-3-3h3m7.5 3h3m-3-3h3" />
                                        </svg>
                                        <span class="text-xs font-bold text-gray-900">Ambil Sendiri</span>
                                        <span class="text-[10px] text-gray-500">Ambil ke kantin cafe</span>
                                    </label>
                                </div>
                                @error('tipe_pengiriman') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            {{-- Informasi Lokasi Pengantaran Berdasarkan Profil Siswa --}}
                            @if($tipe_pengiriman === 'antar')
                                @if(!empty(auth()->user()->kelas) && !empty(auth()->user()->jurusan) && !empty(auth()->user()->ruangan))
                                    <div class="p-3.5 bg-brand-50/70 border border-brand-200 rounded-xl space-y-1 text-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-brand-900 flex items-center gap-1.5">
                                                <svg class="size-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                Tujuan Pengantaran:
                                            </span>
                                            <a href="{{ route('profile.show') }}" target="_blank" class="text-[11px] font-bold text-brand-700 hover:underline">
                                                Ubah di Profil &rarr;
                                            </a>
                                        </div>
                                        <div class="text-sm font-black text-brand-950 pl-5">
                                            {{ auth()->user()->ruangan }} <span class="font-semibold text-xs text-brand-700">(Kelas {{ auth()->user()->kelas }} {{ auth()->user()->jurusan }})</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl space-y-2">
                                        <div class="flex items-start gap-2 text-xs text-rose-800 font-semibold">
                                            <span class="text-base leading-none">⚠️</span>
                                            <div>
                                                <div>Data Kelas, Jurusan, atau Ruangan Anda belum lengkap!</div>
                                                <div class="text-[11px] font-normal text-rose-700 mt-0.5">Untuk menggunakan layanan antar ke kelas, silakan lengkapi data ruangan Anda di menu profil terlebih dahulu.</div>
                                            </div>
                                        </div>
                                        <div class="pt-1 pl-6">
                                            <a href="{{ route('profile.show') }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold shadow-xs transition">
                                                Lengkapi Data di Profil &rarr;
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- Pilihan Metode Pembayaran --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-2">Metode Pembayaran</label>
                                <div class="space-y-2">
                                    {{-- Saldo --}}
                                    @if(auth()->user()->is_verified)
                                        <label class="cursor-pointer border rounded-xl p-3 flex items-center justify-between transition {{ $metode_pembayaran === 'saldo' ? 'border-brand-600 bg-brand-50/60 ring-1 ring-brand-600' : 'border-gray-200 hover:bg-gray-50' }}">
                                            <div class="flex items-center gap-3">
                                                <input type="radio" wire:model.live="metode_pembayaran" value="saldo" class="text-brand-600 focus:ring-brand-500" />
                                                <div>
                                                    <div class="text-xs font-bold text-gray-900">Saldo Cafe Siswa</div>
                                                    <div class="text-[11px] text-gray-500">Saldo saat ini: <strong class="text-emerald-700">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</strong></div>
                                                </div>
                                            </div>
                                            @if(auth()->user()->saldo < $cartTotal)
                                                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">Kurang</span>
                                            @else
                                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Cukup</span>
                                            @endif
                                        </label>
                                    @else
                                        <div class="border border-dashed border-gray-200 bg-gray-50/80 rounded-xl p-3 flex items-center justify-between opacity-75">
                                            <div class="flex items-center gap-3">
                                                <div class="size-4 rounded-full border border-gray-300 bg-gray-200"></div>
                                                <div>
                                                    <div class="text-xs font-bold text-gray-500 line-through">Saldo Cafe Siswa</div>
                                                    <div class="text-[10px] text-rose-600 font-semibold">Belum Terverifikasi (Gunakan QRIS/VA atau COD)</div>
                                                </div>
                                            </div>
                                            <span class="text-[10px] font-bold text-gray-500 bg-gray-200 px-2 py-0.5 rounded">Terkunci</span>
                                        </div>
                                    @endif

                                    {{-- Xendit Online Payment (QRIS, VA, E-Wallet) --}}
                                    {{-- <label class="cursor-pointer border rounded-xl p-3 flex items-center justify-between transition {{ $metode_pembayaran === 'xendit' ? 'border-brand-600 bg-brand-50/60 ring-1 ring-brand-600' : 'border-gray-200 hover:bg-gray-50' }}">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" wire:model.live="metode_pembayaran" value="xendit" class="text-brand-600 focus:ring-brand-500" />
                                            <div>
                                                <div class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
                                                    <span>Bayar Online (QRIS / VA / E-Wallet)</span>
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">Xendit</span>
                                                </div>
                                                <div class="text-[11px] text-gray-500">GoPay, ShopeePay, DANA, OVO, Virtual Account BCA/BRI/BNI/Mandiri</div>
                                            </div>
                                        </div>
                                    </label> --}}

                                    {{-- COD --}}
                                    <label class="cursor-pointer border rounded-xl p-3 flex items-center justify-between transition {{ $metode_pembayaran === 'cod' ? 'border-brand-600 bg-brand-50/60 ring-1 ring-brand-600' : 'border-gray-200 hover:bg-gray-50' }}">
                                        <div class="flex items-center gap-3">
                                            <input type="radio" wire:model.live="metode_pembayaran" value="cod" class="text-brand-600 focus:ring-brand-500" />
                                            <div>
                                                <div class="text-xs font-bold text-gray-900">COD / Tunai di Tempat</div>
                                                <div class="text-[11px] text-gray-500">Bayar langsung saat pesanan diterima</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @error('metode_pembayaran') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Catatan --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                                <textarea
                                    wire:model="catatan"
                                    rows="2"
                                    placeholder="Contoh: Sambal dipisah, es sedikit saja..."
                                    class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                                ></textarea>
                                @error('catatan') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-100">
                            <button
                                type="button"
                                wire:click="closeCheckout"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-100 transition"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-brand-500/30 transition disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="checkout">Pesan Sekarang (Rp {{ number_format($cartTotal, 0, ',', '.') }})</span>
                                <span wire:loading wire:target="checkout">Memproses Pesanan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
