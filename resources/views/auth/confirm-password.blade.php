<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Konfirmasi Password</h2>
            <p class="text-sm text-gray-600 mt-1">Area aman. Konfirmasikan password Anda sebelum melanjutkan.</p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" autofocus placeholder="••••••••" />
            </div>

            <div class="pt-2">
                <x-button class="w-full justify-center py-2.5">
                    {{ __('Konfirmasi') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
