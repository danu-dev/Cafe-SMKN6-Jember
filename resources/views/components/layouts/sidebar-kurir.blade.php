<x-layouts.sidebar-link href="{{ route('kurir.dashboard') }}" :active="request()->routeIs('kurir.dashboard')">
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
    Tugas Kurir
</div>

<x-layouts.sidebar-link href="{{ route('kurir.deliveries.index') }}" :active="request()->routeIs('kurir.deliveries.*')">
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center">
            <svg class="size-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75A1.125 1.125 0 0 0 13.125 2.625H3.375A1.125 1.125 0 0 0 2.25 3.75v10.5c0 .621.504 1.125 1.125 1.125H5.25m9-7.5H2.25" />
            </svg>
            {{ __('Daftar Pengantaran') }}
        </div>
        @php
            $activeCount = \App\Models\Order::where('kurir_id', auth()->id())
                ->whereIn('status', ['siap', 'diantar'])
                ->count();
        @endphp
        @if($activeCount > 0)
            <span class="inline-flex items-center justify-center size-5 rounded-full text-[10px] font-bold bg-blue-600 text-white animate-pulse shadow-2xs">
                {{ $activeCount }}
            </span>
        @endif
    </div>
</x-layouts.sidebar-link>
