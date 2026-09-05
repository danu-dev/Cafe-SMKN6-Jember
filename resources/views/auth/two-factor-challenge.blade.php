<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Autentikasi Dua Faktor</h2>
            <p class="text-sm text-gray-600 mt-1" x-show="! recovery">
                Masukkan kode autentikasi dari aplikasi authenticator Anda.
            </p>
            <p class="text-sm text-gray-600 mt-1" x-cloak x-show="recovery">
                Masukkan salah satu kode pemulihan darurat Anda.
            </p>
        </div>

        <x-validation-errors class="mb-4" />

        <div x-data="{ recovery: false }">
            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-4">
                @csrf

                <div x-show="! recovery">
                    <x-label for="code" value="{{ __('Kode Autentikasi') }}" />
                    <x-input id="code" class="block mt-1 w-full" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" placeholder="123456" />
                </div>

                <div x-cloak x-show="recovery">
                    <x-label for="recovery_code" value="{{ __('Kode Pemulihan') }}" />
                    <x-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                </div>

                <div class="flex items-center justify-between text-sm">
                    <button type="button" class="text-indigo-600 hover:underline cursor-pointer"
                            x-show="! recovery"
                            x-on:click="
                                recovery = true;
                                $nextTick(() => { $refs.recovery_code.focus() })
                            ">
                        {{ __('Gunakan kode pemulihan') }}
                    </button>

                    <button type="button" class="text-indigo-600 hover:underline cursor-pointer"
                            x-cloak
                            x-show="recovery"
                            x-on:click="
                                recovery = false;
                                $nextTick(() => { $refs.code.focus() })
                            ">
                        {{ __('Gunakan kode authenticator') }}
                    </button>
                </div>

                <div class="pt-2">
                    <x-button class="w-full justify-center py-2.5">
                        {{ __('Masuk') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
