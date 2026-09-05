<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Pengaturan Toko') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Kelola identitas dan pengaturan kantin.') }}
            </p>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    {{-- Settings Form --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <form wire:submit="save">
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Identitas Cafe & Kantin</h3>
                    <p class="text-xs text-gray-500 mb-4">Informasi umum yang ditampilkan kepada siswa.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Kantin / Cafe</label>
                            <input
                                type="text"
                                wire:model="store_name"
                                class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                            />
                            @error('store_name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kontak WhatsApp Kantin</label>
                            <input
                                type="text"
                                wire:model="whatsapp_contact"
                                placeholder="Contoh: 081234567890"
                                class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                            />
                            @error('whatsapp_contact') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Deskripsi Singkat</label>
                            <textarea
                                wire:model="store_description"
                                rows="2"
                                class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                            ></textarea>
                            @error('store_description') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 mb-1">Otomasi Pesanan</h3>
                    <p class="text-xs text-gray-500 mb-4">Pengaturan otomatisasi alur pesanan masuk.</p>

                    <div class="flex items-center gap-3">
                        <input
                            type="checkbox"
                            id="auto_accept"
                            wire:model="auto_accept_orders"
                            class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 size-4"
                        />
                        <label for="auto_accept" class="text-sm text-gray-700 font-medium cursor-pointer">
                            Otomatis ubah status pesanan baru ke <strong class="text-gray-900">Diproses</strong> (tanpa menunggu konfirmasi manual)
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 sm:px-8 py-4 flex justify-end border-t border-gray-100">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-5 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 shadow-sm shadow-brand-500/30 transition disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
