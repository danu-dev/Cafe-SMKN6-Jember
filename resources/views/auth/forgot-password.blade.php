<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Lupa Password</h2>
            <p class="text-sm text-gray-600 mt-1">Masukkan email Anda untuk menerima tautan reset password.</p>
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            </div>

            <div class="pt-2">
                <x-button class="w-full justify-center py-2.5">
                    {{ __('Kirim Tautan Reset Password') }}
                </x-button>
            </div>

            <div class="text-center text-sm text-gray-600 pt-2">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Kembali ke halaman Masuk</a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
