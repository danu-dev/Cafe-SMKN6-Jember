<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-brand-950">Daftar Menu Cafe</h1>
            <p class="text-sm text-brand-600 mt-0.5">Kelola seluruh makanan, minuman, harga, stok, dan ketersediaan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.kategori.index') }}" class="px-4 py-2 text-sm font-semibold text-brand-800 bg-white border border-brand-200 hover:bg-brand-50 rounded-xl transition shadow-xs flex items-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Kelola Kategori
            </a>
            <button wire:click="openCreate" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-sm flex items-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Menu
            </button>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-brand-100 shadow-xs flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
        <!-- Search Input -->
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-400">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari menu berdasarkan nama atau deskripsi..."
                   class="w-full pl-10 pr-4 py-2 text-sm rounded-xl border-brand-200 focus:border-brand-500 focus:ring-brand-500 placeholder:text-gray-400">
        </div>

        <div class="flex items-center gap-3">
            <!-- Category Filter -->
            <select wire:model.live="kategoriFilter" class="text-sm rounded-xl border-brand-200 focus:border-brand-500 focus:ring-brand-500 text-brand-900">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                @endforeach
            </select>

            <!-- Availability Filter -->
            <select wire:model.live="statusFilter" class="text-sm rounded-xl border-brand-200 focus:border-brand-500 focus:ring-brand-500 text-brand-900">
                <option value="">Semua Status</option>
                <option value="1">Ready</option>
                <option value="0">Not Ready</option>
            </select>
        </div>
    </div>

    <!-- Menu Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse ($menus as $item)
            <div class="bg-white rounded-2xl border border-brand-100 shadow-xs overflow-hidden flex flex-col group hover:shadow-md transition">
                <!-- Image Container -->
                <div class="relative h-44 bg-brand-50 overflow-hidden">
                    @if ($item->gambar)
                        <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-brand-300 bg-brand-50/70">
                            <svg class="size-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                            </svg>
                            <span class="text-xs font-medium mt-1">Tanpa Gambar</span>
                        </div>
                    @endif

                    <!-- Category Badge -->
                    <span class="absolute top-3 left-3 px-2.5 py-1 text-[11px] font-bold rounded-lg bg-white/90 text-brand-900 backdrop-blur-xs shadow-xs">
                        {{ $item->kategori->nama ?? 'Umum' }}
                    </span>
                </div>

                <!-- Info Body -->
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="text-base font-bold text-brand-950 line-clamp-1" title="{{ $item->nama }}">
                                {{ $item->nama }}
                            </h3>
                        </div>
                        <p class="text-xs text-brand-500 mt-1 line-clamp-2 min-h-8">
                            {{ $item->deskripsi ?: 'Tidak ada deskripsi.' }}
                        </p>
                    </div>

                    <div class="pt-3 mt-3 border-t border-brand-50 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="text-xs text-brand-500 font-medium">Stok: <span class="font-bold text-brand-950">{{ $item->stok }}</span></div>
                            <div class="text-base font-black text-brand-900">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Status Switch & Actions Row -->
                        <div class="flex items-center justify-between pt-2 border-t border-dashed border-brand-100">
                            <!-- Toggle Ready Switch Button -->
                            <button
                                wire:click="toggleAvailability({{ $item->id }})"
                                type="button"
                                class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-xl border text-xs font-bold transition {{ $item->is_available ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' }}"
                                title="Klik untuk ubah status Ready / Not Ready"
                            >
                                <span class="size-2 rounded-full {{ $item->is_available ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                                <span>{{ $item->is_available ? 'Ready' : 'Not Ready' }}</span>
                                <!-- Mini Switch Pill -->
                                <span class="relative inline-flex h-4 w-7 shrink-0 rounded-full transition-colors {{ $item->is_available ? 'bg-emerald-600' : 'bg-gray-300' }}">
                                    <span class="inline-block size-3.5 transform rounded-full bg-white shadow-xs transition {{ $item->is_available ? 'translate-x-3' : 'translate-x-0.5' }} mt-[1px]"></span>
                                </span>
                            </button>

                            <!-- Edit & Delete Actions -->
                            <div class="flex items-center gap-1">
                                <button wire:click="openEdit({{ $item->id }})" class="p-2 text-brand-700 hover:text-brand-950 hover:bg-brand-50 rounded-xl transition" title="Edit Menu">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $item->id }})" 
                                        wire:confirm="Yakin ingin menghapus menu '{{ $item->nama }}'?"
                                        class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-xl transition" 
                                        title="Hapus Menu">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-brand-100">
                <div class="size-12 rounded-full bg-brand-50 text-brand-400 mx-auto flex items-center justify-center mb-3">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-brand-900">Tidak ada menu yang ditemukan</p>
                <p class="text-xs text-brand-400 mt-1">Coba ubah kata kunci pencarian atau tambah menu baru.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div>
        {{ $menus->links() }}
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if ($modalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            <div wire:click="$set('modalOpen', false)" class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"></div>

            <div class="relative bg-white rounded-2xl p-6 w-full max-w-lg border border-brand-100 shadow-2xl z-10 my-8">
                <div class="flex items-center justify-between pb-4 border-b border-brand-100">
                    <h3 class="text-lg font-bold text-brand-950">
                        {{ $editingId ? 'Edit Menu' : 'Tambah Menu Baru' }}
                    </h3>
                    <button wire:click="$set('modalOpen', false)" class="text-brand-400 hover:text-brand-950">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="mt-4 space-y-4">
                    <!-- Kategori & Nama -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-brand-900 uppercase mb-1">Kategori</label>
                            <select wire:model="kategori_id" class="w-full rounded-xl border-brand-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-brand-900 uppercase mb-1">Nama Menu</label>
                            <input type="text" wire:model="nama" placeholder="Es Teh Manis" class="w-full rounded-xl border-brand-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('nama') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Harga & Stok -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-brand-900 uppercase mb-1">Harga (Rp)</label>
                            <input type="number" wire:model="harga" placeholder="Contoh: 15000" class="w-full rounded-xl border-brand-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('harga') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-brand-900 uppercase mb-1">Stok Awal</label>
                            <input type="number" wire:model="stok" placeholder="Contoh: 20" class="w-full rounded-xl border-brand-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                            @error('stok') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label class="block text-xs font-semibold text-brand-900 uppercase mb-1">Deskripsi Menu</label>
                        <textarea wire:model="deskripsi" rows="2" placeholder="Jelaskan porsi, topping, atau rasa..." class="w-full rounded-xl border-brand-200 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                        @error('deskripsi') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Upload Gambar -->
                    <div>
                        <label class="block text-xs font-semibold text-brand-900 uppercase mb-1">Gambar Menu (Opsional)</label>
                        <input type="file" wire:model="gambar" accept="image/*" class="w-full text-xs text-brand-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                        @error('gambar') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror

                        <div wire:loading wire:target="gambar" class="text-xs text-brand-600 mt-1">Mengunggah gambar...</div>

                        @if ($gambar)
                            <div class="mt-2 text-xs text-brand-600">
                                <span class="font-semibold">Preview Baru:</span>
                                <img src="{{ $gambar->temporaryUrl() }}" class="size-20 object-cover rounded-xl mt-1 border border-brand-200">
                            </div>
                        @elseif ($existingGambar)
                            <div class="mt-2 text-xs text-brand-600">
                                <span class="font-semibold">Gambar Saat Ini:</span>
                                <img src="{{ asset('storage/' . $existingGambar) }}" class="size-20 object-cover rounded-xl mt-1 border border-brand-200">
                            </div>
                        @endif
                    </div>

                    <!-- Status Ready / Not Ready Toggle -->
                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-brand-900 uppercase mb-2">Status Stok / Ketersediaan</label>
                        <div class="flex items-center justify-between p-3.5 rounded-xl border {{ $is_available ? 'bg-emerald-50/60 border-emerald-200' : 'bg-rose-50/60 border-rose-200' }} transition">
                            <div class="flex items-center gap-2.5">
                                <span class="size-2.5 rounded-full {{ $is_available ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                                <div>
                                    <div class="text-sm font-bold {{ $is_available ? 'text-emerald-900' : 'text-rose-900' }}">
                                        {{ $is_available ? 'Ready (Tersedia)' : 'Not Ready (Habis / Tidak Dijual)' }}
                                    </div>
                                    <div class="text-xs {{ $is_available ? 'text-emerald-700' : 'text-rose-700' }}">
                                        {{ $is_available ? 'Menu dapat dipesan oleh siswa' : 'Menu tidak akan muncul di daftar pesanan siswa' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Switch Button -->
                            <button
                                type="button"
                                wire:click="$toggle('is_available')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $is_available ? 'bg-emerald-600' : 'bg-gray-300' }}"
                                role="switch"
                                aria-checked="{{ $is_available ? 'true' : 'false' }}"
                            >
                                <span class="pointer-events-none inline-block size-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $is_available ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex justify-end gap-3 pt-4 border-t border-brand-100">
                        <button type="button" wire:click="$set('modalOpen', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-sm transition flex items-center gap-2">
                            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Simpan Perubahan' : 'Tambah Menu' }}</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
