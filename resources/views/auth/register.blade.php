<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-brand-950">Daftar Akun Cafe</h2>
            <p class="text-sm text-brand-700 mt-1">Buat akun siswa untuk mulai memesan</p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="username" value="{{ __('Username') }}" class="text-brand-900" />
                <x-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" placeholder="Username" />
            </div>

            <div>
                <x-label for="name" value="{{ __('Nama Lengkap') }}" class="text-brand-900" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autocomplete="name" placeholder="Nama Lengkap" />
            </div>

            <div>
                <x-label for="email" value="{{ __('Email') }}" class="text-brand-900" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" placeholder="Email" />
            </div>

            <div>
                <x-label for="kelas" value="{{ __('Kelas') }}" class="text-brand-900" />
                <select id="kelas" name="kelas" required class="block mt-1 w-full border-amber-200 focus:border-brand-500 focus:ring-brand-500 rounded-lg shadow-sm text-sm">
                    <option value="" disabled {{ old('kelas') ? '' : 'selected' }}>Pilih Kelas</option>
                    <option value="10" {{ old('kelas') == '10' ? 'selected' : '' }}>Kelas 10</option>
                    <option value="11" {{ old('kelas') == '11' ? 'selected' : '' }}>Kelas 11</option>
                    <option value="12" {{ old('kelas') == '12' ? 'selected' : '' }}>Kelas 12</option>
                </select>
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" class="text-brand-900" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            </div>

            <div>
                <x-label for="password_confirmation" value="{{ __('Konfirmasi Password') }}" class="text-brand-900" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div>
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />
                            <div class="ms-2 text-sm text-brand-800">
                                {!! __('Saya setuju dengan Syarat & Ketentuan serta Kebijakan Privasi') !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="pt-2">
                <x-button class="w-full justify-center py-3 text-sm">
                    {{ __('Daftar') }}
                </x-button>
            </div>

            <div class="text-center text-sm text-brand-800 pt-2">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-brand-600 font-semibold hover:text-brand-800 hover:underline">Masuk di sini</a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
