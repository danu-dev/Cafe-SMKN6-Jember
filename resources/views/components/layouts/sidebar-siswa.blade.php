<x-layouts.sidebar-link href="{{ route('siswa.dashboard') }}" :active="request()->routeIs('siswa.dashboard')">
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center">
            <svg class="size-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
            </svg>
            {{ __('Dashboard') }}
        </div>
    </div>
</x-layouts.sidebar-link>

<div class="pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-brand-400 px-3">
    Pesan & Belanja
</div>

<x-layouts.sidebar-link href="{{ route('siswa.menu.index') }}" :active="request()->routeIs('siswa.menu.*')">
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center">
            <svg class="size-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
            </svg>
            {{ __('Pesan Menu') }}
        </div>
        @php
            $cartCount = array_sum(array_column(session('siswa_cart', []), 'qty'));
        @endphp
        @if($cartCount > 0)
            <span class="inline-flex items-center justify-center size-5 rounded-full text-[10px] font-bold bg-emerald-600 text-white shadow-2xs">
                {{ $cartCount }}
            </span>
        @endif
    </div>
</x-layouts.sidebar-link>

<x-layouts.sidebar-link href="{{ route('siswa.orders.index') }}" :active="request()->routeIs('siswa.orders.*')">
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center">
            <svg class="size-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
            </svg>
            {{ __('Pesanan Saya') }}
        </div>
        @php
            $activeOrdersCount = \App\Models\Order::where('user_id', auth()->id())
                ->whereIn('status', ['menunggu', 'diproses', 'siap', 'diantar'])
                ->count();
        @endphp
        @if($activeOrdersCount > 0)
            <span class="inline-flex items-center justify-center size-5 rounded-full text-[10px] font-bold bg-blue-600 text-white animate-pulse shadow-2xs">
                {{ $activeOrdersCount }}
            </span>
        @endif
    </div>
</x-layouts.sidebar-link>

<div class="pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-brand-400 px-3">
    Keuangan
</div>

<x-layouts.sidebar-link href="{{ route('siswa.saldo.index') }}" :active="request()->routeIs('siswa.saldo.*')">
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center">
            <svg class="size-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
            </svg>
            {{ __('Saldo Saya') }}
        </div>
        <span class="text-xs font-bold text-emerald-600 font-mono">
            Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}
        </span>
    </div>
</x-layouts.sidebar-link>
