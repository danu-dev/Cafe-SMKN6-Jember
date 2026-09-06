<div class="relative" x-data="{ open: false }" wire:poll.10s>
    {{-- Bell Button --}}
    <button
        @click="open = ! open"
        type="button"
        class="relative p-2 text-brand-700 hover:text-brand-950 hover:bg-brand-50 rounded-xl transition focus:outline-none"
        title="Notifikasi"
    >
        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>

        {{-- Unread Badge Circle --}}
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex items-center justify-center size-4 rounded-full bg-rose-600 text-white text-[9px] font-black animate-pulse shadow-2xs">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Menu --}}
    <div
        x-show="open"
        x-cloak
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-brand-100 z-50 overflow-hidden"
    >
        {{-- Dropdown Header --}}
        <div class="px-4 py-3 bg-brand-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider">Notifikasi</span>
                @if($unreadCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500 text-white">
                        {{ $unreadCount }} Baru
                    </span>
                @endif
            </div>
            @if($unreadCount > 0)
                <button
                    wire:click="markAllAsRead"
                    type="button"
                    class="text-[11px] text-brand-200 hover:text-white underline font-medium transition"
                >
                    Tandai Semua Dibaca
                </button>
            @endif
        </div>

        {{-- Notification List --}}
        <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
            @forelse($notifications as $notif)
                @php
                    $data = $notif->data;
                    $isUnread = $notif->unread();
                    $type = $data['type'] ?? 'info';
                    $iconBg = match($type) {
                        'order' => 'bg-brand-50 text-brand-700',
                        'delivery' => 'bg-blue-50 text-blue-700',
                        'balance' => 'bg-emerald-50 text-emerald-700',
                        'verification' => 'bg-purple-50 text-purple-700',
                        default => 'bg-gray-50 text-gray-700'
                    };
                @endphp
                <div
                    wire:click="markAsRead('{{ $notif->id }}', '{{ $data['action_url'] ?? '' }}')"
                    class="p-3.5 flex items-start gap-3 hover:bg-brand-50/60 transition cursor-pointer {{ $isUnread ? 'bg-brand-50/30' : '' }}"
                >
                    <div class="size-8 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0 font-bold text-xs mt-0.5">
                        @if($type === 'order')
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @elseif($type === 'delivery')
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        @elseif($type === 'balance')
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @else
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1">
                            <h4 class="text-xs font-bold text-gray-900 truncate {{ $isUnread ? 'text-brand-950' : '' }}">
                                {{ $data['title'] ?? 'Notifikasi' }}
                            </h4>
                            @if($isUnread)
                                <span class="size-1.5 rounded-full bg-brand-600 shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-600 mt-0.5 line-clamp-2">
                            {{ $data['message'] ?? '' }}
                        </p>
                        <span class="text-[10px] text-gray-400 mt-1 block font-medium">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <div class="size-10 rounded-full bg-brand-50 text-brand-400 mx-auto flex items-center justify-center mb-2">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-gray-700">Belum ada notifikasi</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Aktivitas baru akan muncul di sini secara real-time.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
