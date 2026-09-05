<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Manajemen Kurir') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Kelola akun dan pantau aktivitas pengantaran kurir cafe.') }}
            </p>
        </div>
        <button
            wire:click="openCreateModal"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 shadow-sm shadow-brand-500/30 transition gap-2"
        >
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('Tambah Kurir') }}
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold uppercase text-gray-400">Total Kurir</div>
            <div class="text-2xl font-black text-gray-900 mt-1">{{ $totalKurir }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $kurirAktif }} aktif beroperasi</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold uppercase text-gray-400">Kurir Aktif</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $kurirAktif }}</div>
            <div class="text-xs text-gray-500 mt-1">Siap terima order</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold uppercase text-gray-400">Diantar Hari Ini</div>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ $antarHariIni }}</div>
            <div class="text-xs text-gray-500 mt-1">Order tipe antar</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold uppercase text-gray-400">Selesai Hari Ini</div>
            <div class="text-2xl font-black text-purple-600 mt-1">{{ $selesaiHariIni }}</div>
            <div class="text-xs text-gray-500 mt-1">Berhasil diantar</div>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6 flex justify-between items-center">
        <div class="w-full max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari nama, username, email kurir..."
                class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
            />
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

    {{-- Kurir Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kurir</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Sedang Diantar</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Selesai Hari Ini</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Antar</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($kurirs as $kurir)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-sm">
                                        {{ substr($kurir->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm">{{ $kurir->name }}</div>
                                        <div class="text-xs text-gray-400 font-mono">{{ '@'.$kurir->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $kurir->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $kurir->pengantaran_aktif > 0 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $kurir->pengantaran_aktif }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                    {{ $kurir->selesai_hari_ini }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-700">
                                {{ $kurir->total_pengantaran }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button
                                    wire:click="toggleActive({{ $kurir->id }})"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold cursor-pointer transition {{ $kurir->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 ring-1 ring-rose-600/20 hover:bg-rose-100' }}"
                                >
                                    {{ $kurir->is_active ? '● Aktif' : '○ Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button
                                    wire:click="openRiwayat({{ $kurir->id }})"
                                    class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg text-xs font-semibold transition"
                                >
                                    Riwayat
                                </button>
                                <button
                                    wire:click="openEditModal({{ $kurir->id }})"
                                    class="text-brand-600 hover:text-brand-900 bg-brand-50 hover:bg-brand-100 px-2.5 py-1 rounded-lg text-xs font-semibold transition"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 text-sm">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.25V3.75A1.125 1.125 0 0 0 13.125 2.625H3.375A1.125 1.125 0 0 0 2.25 3.75v10.5c0 .621.504 1.125 1.125 1.125H5.25m9-7.5H2.25" />
                                </svg>
                                Belum ada data kurir.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kurirs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $kurirs->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Create / Edit Kurir --}}
    @if($showFormModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeFormModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <form wire:submit="save">
                        <div class="bg-white p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                {{ $editingKurirId ? 'Edit Data Kurir' : 'Tambah Kurir Baru' }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                                    <input
                                        type="text"
                                        wire:model="name"
                                        class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                                        placeholder="Nama Lengkap"
                                    />
                                    @error('name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Username</label>
                                    <input
                                        type="text"
                                        wire:model="username"
                                        class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                                        placeholder="Username"
                                    />
                                    @error('username') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email</label>
                                    <input
                                        type="email"
                                        wire:model="email"
                                        class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                                        placeholder="Email"
                                    />
                                    @error('email') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                        Password {{ $editingKurirId ? '(Kosongkan jika tidak diubah)' : '' }}
                                    </label>
                                    <input
                                        type="password"
                                        wire:model="password"
                                        class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                                        placeholder="Minimal 8 karakter"
                                    />
                                    @error('password') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex items-center gap-2 pt-2">
                                    <input
                                        type="checkbox"
                                        id="is_active"
                                        wire:model="is_active"
                                        class="rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                                    />
                                    <label for="is_active" class="text-sm text-gray-700 font-medium">Status Aktif</label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                            <button
                                type="button"
                                wire:click="closeFormModal"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-100 transition"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700 shadow-sm shadow-brand-500/30 transition disabled:opacity-50"
                            >
                                {{ $editingKurirId ? 'Simpan Perubahan' : 'Tambah Kurir' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Riwayat Pengantaran --}}
    @if($showRiwayatModal && $selectedKurir)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeRiwayat"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-brand-900 px-6 py-5 text-white flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Riwayat Pengantaran: {{ $selectedKurir->name }}</h3>
                            <p class="text-xs text-brand-200 mt-0.5">{{ '@'.$selectedKurir->username }} • {{ $selectedKurir->email }}</p>
                        </div>
                        <button wire:click="closeRiwayat" class="text-brand-300 hover:text-white transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 max-h-96 overflow-y-auto">
                        <div class="space-y-3">
                            @forelse($selectedKurir->deliveries->take(15) as $delivery)
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between text-sm">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-gray-900">#{{ $delivery->kode_pesanan }}</span>
                                            <span class="text-xs text-gray-400">• {{ $delivery->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="text-xs text-gray-600 mt-1">
                                            Tujuan: <strong class="text-gray-800">{{ $delivery->kelas_tujuan ?? '-' }}</strong> • Pemesan: {{ $delivery->user->name ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $delivery->status === 'selesai' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ ucfirst($delivery->status) }}
                                        </span>
                                        <div class="text-xs font-bold text-gray-900 mt-1">
                                            Rp {{ number_format($delivery->total_harga, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 text-center py-8">Belum ada riwayat pengantaran untuk kurir ini.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-100">
                        <button
                            wire:click="closeRiwayat"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold hover:bg-gray-300 transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
