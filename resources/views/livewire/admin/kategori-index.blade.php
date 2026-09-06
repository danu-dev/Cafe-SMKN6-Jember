<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-brand-600 font-medium mb-1">
                <a href="{{ route('admin.menu.index') }}" class="hover:underline">Menu</a>
                <span>/</span>
                <span>Kategori</span>
            </div>
            <h1 class="text-2xl font-bold text-brand-950">Kategori Menu</h1>
            <p class="text-sm text-brand-600 mt-0.5">Kelola kategori makanan, minuman, dan snack cafe.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.menu.index') }}" class="px-4 py-2 text-sm font-semibold text-brand-800 bg-white border border-brand-200 hover:bg-brand-50 rounded-xl transition shadow-xs">
                &larr; Kembali ke Menu
            </a>
            <button wire:click="openCreate" class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-sm flex items-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kategori
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
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

    @if (session()->has('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-brand-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-brand-50/60 text-brand-900 text-xs uppercase font-semibold border-b border-brand-100">
                    <tr>
                        <th class="px-6 py-3.5">Icon / Gambar</th>
                        <th class="px-6 py-3.5">Nama Kategori</th>
                        <th class="px-6 py-3.5">Slug</th>
                        <th class="px-6 py-3.5">Jumlah Menu</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-50">
                    @forelse ($categories as $kategori)
                        <tr class="hover:bg-brand-50/30 transition">
                            <td class="px-6 py-4">
                                @if($kategori->gambar)
                                    <img src="{{ asset('storage/' . $kategori->gambar) }}" alt="{{ $kategori->nama }}" class="size-10 object-cover rounded-xl border border-brand-200">
                                @elseif($kategori->icon)
                                    <div class="size-10 rounded-xl bg-brand-50 border border-brand-200 flex items-center justify-center text-lg">
                                        @switch($kategori->icon)
                                            @case('utensils') 🍴 @break
                                            @case('cup-hot') ☕ @break
                                            @case('wine') 🥤 @break
                                            @case('cookie') 🍪 @break
                                            @case('cake') 🍰 @break
                                            @case('flame') 🔥 @break
                                            @case('burger') 🍔 @break
                                            @case('ice-cream') 🍨 @break
                                            @case('sparkles') ✨ @break
                                            @default 🏷️
                                        @endswitch
                                    </div>
                                @else
                                    <div class="size-10 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-xs text-gray-400 font-bold">
                                        -
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold text-brand-950">
                                {{ $kategori->nama }}
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-brand-500">
                                {{ $kategori->slug }}
                            </td>
                            <td class="px-6 py-4 text-brand-700">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-100 text-brand-800">
                                    {{ $kategori->menus_count }} menu
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="openEdit({{ $kategori->id }})" class="p-1.5 text-brand-600 hover:text-brand-900 hover:bg-brand-100 rounded-lg transition" title="Edit">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $kategori->id }})" 
                                        wire:confirm="Yakin ingin menghapus kategori ini?" 
                                        class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition" 
                                        title="Hapus">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-brand-400">
                                Belum ada kategori menu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Form -->
    @if ($modalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            <div wire:click="$set('modalOpen', false)" class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"></div>

            <div class="relative bg-white rounded-2xl p-6 w-full max-w-md border border-brand-100 shadow-2xl z-10">
                <div class="flex items-center justify-between pb-4 border-b border-brand-100">
                    <h3 class="text-lg font-bold text-brand-950">
                        {{ $editingId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                    </h3>
                    <button wire:click="$set('modalOpen', false)" class="text-brand-400 hover:text-brand-950">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit="save" class="mt-4 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-brand-900 uppercase mb-1">Nama Kategori</label>
                        <input type="text" 
                               wire:model="nama" 
                               placeholder="Makanan Berat, Minuman Dingin"
                               class="w-full rounded-xl border-brand-200 text-sm focus:border-brand-500 focus:ring-brand-500 placeholder:text-gray-400">
                        @error('nama') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-brand-900 uppercase mb-1">Pilih Icon Preset</label>
                        <select wire:model="icon" class="w-full rounded-xl border-brand-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">-- Tanpa Icon Preset --</option>
                            @foreach($availableIcons as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('icon') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-brand-900 uppercase mb-1">Atau Upload Gambar Kategori (Opsional)</label>
                        <input type="file" 
                               wire:model="gambar" 
                               accept="image/*"
                               class="w-full text-xs text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                        @error('gambar') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror

                        <div wire:loading wire:target="gambar" class="text-xs text-brand-600 mt-1">Mengunggah file...</div>

                        @if ($gambar)
                            <div class="mt-2 flex items-center gap-3">
                                <span class="text-xs text-brand-600">Preview:</span>
                                <img src="{{ $gambar->temporaryUrl() }}" class="size-12 object-cover rounded-xl border border-brand-200">
                            </div>
                        @elseif ($existingGambar)
                            <div class="mt-2 flex items-center gap-3">
                                <span class="text-xs text-brand-600">Gambar saat ini:</span>
                                <img src="{{ asset('storage/' . $existingGambar) }}" class="size-12 object-cover rounded-xl border border-brand-200">
                                <button type="button" wire:click="removeGambar" class="text-xs text-red-600 hover:underline">Hapus Gambar</button>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-brand-100">
                        <button type="button" 
                                wire:click="$set('modalOpen', false)" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-sm transition">
                            {{ $editingId ? 'Simpan Perubahan' : 'Tambah' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
