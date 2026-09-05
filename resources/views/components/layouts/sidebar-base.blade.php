@props(['title' => 'Cafe'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Cafe') }} - {{ $title }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-brand-50/40 text-brand-950">
        <x-banner />

        <div x-data="{ sidebarOpen: false, logoutModalOpen: false }" class="min-h-screen">
            <!-- Mobile Backdrop -->
            <div x-show="sidebarOpen" 
                 x-cloak
                 @click="sidebarOpen = false" 
                 class="fixed inset-0 z-40 bg-black/50 lg:hidden transition-opacity"></div>

            <!-- Fixed Sidebar Container -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
                   class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-brand-100 flex flex-col transition-transform duration-300 ease-in-out">
                <!-- Sidebar Header / Logo -->
                <div class="h-20 px-6 flex items-center justify-center relative border-b border-brand-100 shrink-0">
                    <a href="{{ url('/') }}" class="flex items-center justify-center">
                        <x-application-mark class="h-14 w-auto max-w-[180px]" />
                    </a>
                    <button @click="sidebarOpen = false" class="absolute right-4 lg:hidden text-brand-700 hover:text-brand-950 p-1">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    {{ $sidebarLinks }}
                </nav>
            </aside>

            <!-- Main Content Area with left margin on desktop -->
            <div class="lg:pl-64 flex flex-col min-h-screen">
                <!-- Top Navbar (Desktop & Mobile) -->
                <header class="sticky top-0 z-30 min-h-16 bg-white border-b border-brand-100 flex items-center justify-between px-4 lg:px-8 py-3">
                    <!-- Left: Mobile Toggle + Page Title / Header Slot -->
                    <div class="flex items-center space-x-3 min-w-0 flex-1 mr-4">
                        <button @click="sidebarOpen = true" class="p-2 text-brand-800 hover:text-brand-950 rounded-lg hover:bg-brand-50 lg:hidden shrink-0">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        @if (isset($header))
                            <div class="w-full">
                                {{ $header }}
                            </div>
                        @else
                            <h1 class="text-lg font-bold text-brand-950 truncate">
                                {{ $title }}
                            </h1>
                        @endif
                    </div>

                    <!-- Right: Notification Bell + Profile Dropdown -->
                    @auth
                        <div class="flex items-center gap-2">
                            {{-- Realtime Notification Bell Component with Auto-Polling --}}
                            <livewire:components.notification-bell />

                            {{-- Profile Dropdown --}}
                            <div class="relative" x-data="{ userMenu: false }">
                                <button @click="userMenu = ! userMenu" class="flex items-center space-x-3 p-1 rounded-xl hover:bg-brand-50 transition text-start focus:outline-none">
                                    <div class="size-9 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <div class="hidden sm:block leading-tight">
                                        <div class="text-sm font-semibold text-brand-950 flex items-center">
                                            <span>{{ Auth::user()->name }}</span>
                                            <svg class="size-4 ml-1 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </div>
                                        <div class="text-xs text-brand-600 capitalize font-medium">{{ Auth::user()->role }}</div>
                                    </div>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="userMenu" 
                                     x-cloak
                                     @click.away="userMenu = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-1 border border-brand-100 z-50">
                                    <div class="px-4 py-2 text-xs text-brand-400 uppercase font-semibold border-b border-brand-50">
                                        Akun {{ Auth::user()->name }}
                                    </div>
                                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-brand-900 hover:bg-brand-50">
                                        Profile
                                    </a>
                                    <button type="button" @click="userMenu = false; logoutModalOpen = true" class="w-full text-start block px-4 py-2 text-sm text-red-600 hover:bg-red-50 cursor-pointer">
                                        Log Out
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endauth
                </header>

                <!-- Content Slot -->
                <main class="flex-1 p-6 max-w-7xl w-full mx-auto">
                    {{ $slot }}
                </main>
            </div>

            <!-- Modal Konfirmasi Logout -->
            <div x-show="logoutModalOpen" 
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <!-- Backdrop -->
                <div x-show="logoutModalOpen" 
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="logoutModalOpen = false" 
                     class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"></div>

                <div class="flex min-h-screen items-center justify-center p-4 text-center">
                    <div x-show="logoutModalOpen" 
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6 border border-brand-100">
                        <div class="flex items-center space-x-4">
                            <div class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0">
                                <svg class="size-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Konfirmasi Keluar</h3>
                                <p class="text-sm text-gray-600 mt-1">Apakah Anda yakin ingin keluar dari akun ini?</p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" 
                                    @click="logoutModalOpen = false" 
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                                Batal
                            </button>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <button type="submit" 
                                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-sm transition">
                                    Ya, Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Realtime Toast Notification Popup --}}
        <div 
            x-data="{ 
                toasts: [],
                playNotificationSound() {
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.type = 'sine';
                        osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                        osc.frequency.setValueAtTime(880, audioCtx.currentTime + 0.1); // A5
                        gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                        osc.start();
                        osc.stop(audioCtx.currentTime + 0.3);
                    } catch (e) {}
                }
            }"
            x-on:new-notification.window="
                const id = Date.now() + Math.random();
                toasts.push({
                    id: id,
                    title: $event.detail[0]?.title || 'Notifikasi Baru',
                    message: $event.detail[0]?.message || ''
                });
                playNotificationSound();
                setTimeout(() => {
                    toasts = toasts.filter(t => t.id !== id);
                }, 6000);
            "
            class="fixed top-5 right-5 z-50 space-y-2 pointer-events-none max-w-sm w-full"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div 
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                    class="bg-brand-900 text-white p-4 rounded-2xl shadow-2xl border border-brand-700/50 flex items-start gap-3 pointer-events-auto relative group"
                >
                    <div class="size-8 rounded-xl bg-brand-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                        <svg class="size-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div class="flex-1 min-w-0 pr-4">
                        <h4 class="text-xs font-bold text-white truncate" x-text="toast.title"></h4>
                        <p class="text-[11px] text-brand-200 mt-0.5 line-clamp-3" x-text="toast.message"></p>
                    </div>
                    {{-- Close / Silang Button --}}
                    <button 
                        type="button"
                        @click="toasts = toasts.filter(t => t.id !== toast.id)"
                        class="text-brand-300 hover:text-white p-1 rounded-lg hover:bg-white/10 transition shrink-0 absolute top-2.5 right-2.5"
                        title="Tutup Notifikasi"
                    >
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        @stack('modals')
        @livewireScripts
    </body>
</html>
