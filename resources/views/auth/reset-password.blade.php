<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Reset Password</h2>
            <p class="text-sm text-gray-600 mt-1">Buat password baru untuk akun Anda</p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password Baru') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            </div>

            <div>
                <x-label for="password_confirmation" value="{{ __('Konfirmasi Password Baru') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
            </div>

            <div class="pt-2">
                <x-button class="w-full justify-center py-2.5">
                    {{ __('Simpan Password Baru') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
