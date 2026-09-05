<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-brand-950">Masuk ke Cafe</h2>
            <p class="text-sm text-brand-700 mt-1">Silakan masuk menggunakan akun Anda</p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-700 bg-green-50 p-3 rounded-lg border border-green-200">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email atau Username') }}" class="text-brand-900" />
                <x-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Masukkan email atau username" />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" class="text-brand-900" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>

            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="flex items-center cursor-pointer">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-brand-800">{{ __('Ingat saya') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-brand-600 hover:text-brand-800 hover:underline font-medium" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <div class="pt-2">
                <x-button class="w-full justify-center py-3 text-sm">
                    {{ __('Masuk') }}
                </x-button>
            </div>

            @if (Route::has('register'))
                <div class="text-center text-sm text-brand-800 pt-2">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-brand-600 font-semibold hover:text-brand-800 hover:underline">Daftar sekarang</a>
                </div>
            @endif
        </form>
    </x-authentication-card>
</x-guest-layout>
