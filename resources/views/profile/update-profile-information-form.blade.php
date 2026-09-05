<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('Profile Information') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Update your account\'s profile information and email address.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" id="photo" class="hidden"
                            wire:model.live="photo"
                            x-ref="photo"
                            x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            " />

                <x-label for="photo" value="{{ __('Photo') }}" />

                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-full size-20 object-cover">
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.photo.click()">
                    {{ __('Select A New Photo') }}
                </x-secondary-button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2">
                    {{ __('Your email address is unverified.') }}

                    <button type="button" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" wire:click.prevent="sendEmailVerification">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            @endif
        </div>

        {{-- Data Akademik Siswa SMKN 6 Jember --}}
        @if ($this->user->role === 'siswa')
            <div class="col-span-6 sm:col-span-4 pt-4 mt-2 border-t border-gray-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-brand-900 mb-1">
                    {{ __('Data Kelas & Ruangan Sekolah') }}
                </h4>
                <p class="text-xs text-gray-500 mb-4">
                    {{ __('Perbarui data kelas, jurusan, atau ruang teori jika Anda naik kelas atau berpindah ruangan kelas.') }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Tingkat Kelas --}}
                    <div>
                        <x-label for="kelas" value="{{ __('Tingkat Kelas') }}" />
                        <select id="kelas" class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm text-sm" wire:model="state.kelas">
                            <option value="">-- Pilih Kelas --</option>
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        </select>
                        <x-input-error for="kelas" class="mt-2" />
                    </div>

                    {{-- Jurusan --}}
                    <div>
                        <x-label for="jurusan" value="{{ __('Jurusan SMKN 6 Jember') }}" />
                        <select id="jurusan" class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm text-sm" wire:model="state.jurusan">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach(config('school.jurusan', []) as $code => $label)
                                <option value="{{ $code }}">{{ $code }} - {{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="jurusan" class="mt-2" />
                    </div>

                    {{-- Ruang Teori / Praktik --}}
                    <div class="sm:col-span-2">
                        <x-label for="ruangan" value="{{ __('Ruang Teori / Ruang Kelas Utama') }}" />
                        <select id="ruangan" class="mt-1 block w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm text-sm" wire:model="state.ruangan">
                            <option value="">-- Pilih Ruang Teori / Praktik --</option>
                            @foreach(config('school.ruangan', []) as $group => $rooms)
                                <optgroup label="{{ $group }}">
                                    @foreach($rooms as $code => $roomName)
                                        <option value="{{ $code }}">{{ $roomName }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <x-input-error for="ruangan" class="mt-2" />
                    </div>
                </div>
            </div>
        @endif
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Saved.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Save') }}
        </x-button>
    </x-slot>
</x-form-section>
