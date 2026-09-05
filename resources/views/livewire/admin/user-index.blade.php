<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Kelola Pengguna & Saldo') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Kelola seluruh akun pengguna, peran sistem, dan pengisian saldo kasir siswa.') }}
            </p>
        </div>
    </div>

    {{-- Role Tabs & Filters --}}
    <div class="space-y-4">
        {{-- Role Filter Tabs --}}
        <div class="flex flex-wrap gap-2">
            <button
                wire:click="$set('roleFilter', '')"
                class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $roleFilter === '' ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
            >
                Semua Role ({{ $counts['total'] }})
            </button>
            <button
                wire:click="$set('roleFilter', 'siswa')"
                class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $roleFilter === 'siswa' ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
            >
                Siswa ({{ $counts['siswa'] }})
            </button>
            <button
                wire:click="$set('roleFilter', 'kurir')"
                class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $roleFilter === 'kurir' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
            >
                Kurir ({{ $counts['kurir'] }})
            </button>
            <button
                wire:click="$set('roleFilter', 'admin')"
                class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $roleFilter === 'admin' ? 'bg-purple-600 text-white shadow-sm shadow-purple-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
            >
                Admin ({{ $counts['admin'] }})
            </button>
        </div>

        {{-- Search & Kelas Filter Bar --}}
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="w-full md:w-96 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama, username, email..."
                    class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                />
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                @if($roleFilter === '' || $roleFilter === 'siswa')
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500 font-medium">Kelas:</span>
                        <div class="flex gap-1">
                            <button
                                wire:click="$set('kelasFilter', '')"
                                class="px-2.5 py-1 text-xs font-semibold rounded-lg transition {{ $kelasFilter === '' ? 'bg-brand-100 text-brand-800' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                            >
                                Semua
                            </button>
                            @foreach(['10', '11', '12'] as $kls)
                                <button
                                    wire:click="$set('kelasFilter', '{{ $kls }}')"
                                    class="px-2.5 py-1 text-xs font-semibold rounded-lg transition {{ $kelasFilter === $kls ? 'bg-brand-600 text-white shadow-xs' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                >
                                    {{ $kls }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($search || $roleFilter || $kelasFilter)
                    <button
                        wire:click="resetFilter"
                        class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2.5 py-1 rounded-lg hover:bg-rose-50 border border-rose-200 transition"
                    >
                        Reset Filter
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="size-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif

    {{-- Users Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role & Kelas</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Akun</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- User Info --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="text-[10px] bg-brand-100 text-brand-800 font-bold px-1.5 py-0.2 rounded">Anda</span>
                                            @endif
                                            @if($user->role === 'siswa' && $user->is_verified)
                                                <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-1.5 py-0.2 rounded">Terverifikasi</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $user->username ? '@'.$user->username : '-' }} • {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Role & Kelas / Jurusan --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @php
                                        $roleBadges = [
                                            'admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                            'kurir' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'siswa' => 'bg-brand-50 text-brand-700 border-brand-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border capitalize {{ $roleBadges[$user->role] ?? 'bg-gray-50 text-gray-700' }}">
                                        {{ $user->role }}
                                    </span>
                                    @if($user->role === 'siswa')
                                        @if($user->kelas)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                                                Kelas {{ $user->kelas }}
                                            </span>
                                        @endif
                                        @if($user->jurusan)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                {{ $user->jurusan }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button
                                    wire:click="toggleActive({{ $user->id }})"
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold transition {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}"
                                    title="Klik untuk ubah status"
                                >
                                    <span class="size-1.5 rounded-full mr-1.5 {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-1">
                                @if($user->role === 'siswa')
                                    <button
                                        wire:click="openTopup({{ $user->id }})"
                                        class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition"
                                        title="Top Up Saldo Siswa"
                                    >
                                        + Top Up
                                    </button>
                                    <button
                                        wire:click="openRiwayat({{ $user->id }})"
                                        class="text-gray-600 hover:text-gray-900 text-xs font-semibold px-2 py-1.5 rounded-lg hover:bg-gray-100 transition"
                                        title="Riwayat Mutasi Saldo"
                                    >
                                        Riwayat
                                    </button>
                                @endif

                                <button
                                    wire:click="openEdit({{ $user->id }})"
                                    class="text-brand-600 hover:text-brand-800 text-xs font-semibold px-2 py-1.5 rounded-lg hover:bg-brand-50 transition"
                                >
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm">
                                Tidak ada data pengguna yang sesuai pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Topup Saldo --}}
    @if($showTopupModal && $topupUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeTopup"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <form wire:submit="processTopup">
                        <div class="bg-emerald-700 px-6 py-5 text-white flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold">Top Up Saldo Siswa</h3>
                                <p class="text-xs text-emerald-100 mt-0.5">Pengisian saldo kasir untuk {{ $topupUser->name }}</p>
                            </div>
                            <button type="button" wire:click="closeTopup" class="text-emerald-200 hover:text-white transition">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4">
                            {{-- Info User --}}
                            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 text-xs">
                                <div class="text-emerald-950 font-bold">{{ $topupUser->name }} ({{ $topupUser->username ? '@'.$topupUser->username : '-' }})</div>
                                <div class="text-emerald-700 mt-0.5">{{ $topupUser->kelas ? 'Kelas '.$topupUser->kelas : 'Siswa' }} • {{ $topupUser->email }}</div>
                            </div>

                            {{-- Nominal Input --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nominal Top Up (Rp)</label>
                                <input
                                    type="number"
                                    wire:model="topupAmount"
                                    placeholder="Contoh: 50000"
                                    class="block w-full border border-gray-200 rounded-lg text-base font-bold px-3 py-2 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                />
                                @error('topupAmount') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Quick Amount Buttons --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Pilihan Cepat Nominal</label>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach([10000, 20000, 50000, 100000, 200000, 500000] as $preset)
                                        <button
                                            type="button"
                                            wire:click="$set('topupAmount', '{{ $preset }}')"
                                            class="py-1.5 px-2 text-xs font-bold rounded-lg border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition text-gray-700"
                                        >
                                            Rp {{ number_format($preset, 0, ',', '.') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Keterangan --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Keterangan / Catatan</label>
                                <input
                                    type="text"
                                    wire:model="topupKeterangan"
                                    placeholder="Topup saldo oleh admin/kasir"
                                    class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                />
                                @error('topupKeterangan') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-2 border-t border-gray-100">
                            <button type="button" wire:click="closeTopup" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-100 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 shadow-xs transition">
                                Proses Top Up
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Riwayat Saldo Siswa --}}
    @if($showRiwayatModal && $selectedUserForRiwayat)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeRiwayat"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <div class="bg-brand-900 px-6 py-5 text-white flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Riwayat Mutasi Saldo</h3>
                            <p class="text-xs text-brand-200 mt-0.5">{{ $selectedUserForRiwayat->name }} ({{ $selectedUserForRiwayat->username ? '@'.$selectedUserForRiwayat->username : '-' }})</p>
                        </div>
                        <button type="button" wire:click="closeRiwayat" class="text-brand-300 hover:text-white transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden max-h-80 overflow-y-auto">
                            @forelse($selectedUserForRiwayat->saldoTransactions as $tx)
                                <div class="p-3 bg-white flex justify-between items-center text-xs hover:bg-gray-50">
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            @php
                                                $badges = [
                                                    'topup' => 'bg-emerald-100 text-emerald-800',
                                                    'pembayaran' => 'bg-rose-100 text-rose-800',
                                                    'refund' => 'bg-blue-100 text-blue-800',
                                                ];
                                            @endphp
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $badges[$tx->tipe] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $tx->tipe }}
                                            </span>
                                            <span class="text-gray-400">{{ $tx->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="text-gray-700 mt-0.5">{{ $tx->keterangan ?? '-' }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold {{ $tx->tipe === 'pembayaran' ? 'text-rose-600' : 'text-emerald-600' }}">
                                            {{ $tx->tipe === 'pembayaran' ? '-' : '+' }} Rp {{ number_format($tx->jumlah, 0, ',', '.') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 font-mono">
                                            Rp {{ number_format($tx->saldo_sebelum, 0, ',', '.') }} &rarr; Rp {{ number_format($tx->saldo_sesudah, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-gray-400 text-xs">
                                    Belum ada catatan mutasi transaksi saldo untuk siswa ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end border-t border-gray-100">
                        <button type="button" wire:click="closeRiwayat" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-100 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Edit User Modal --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeEdit"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveUser">
                        <div class="bg-brand-900 px-6 py-5 text-white flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold">Edit Akun Pengguna</h3>
                                <p class="text-xs text-brand-200 mt-0.5">Perbarui profil dan hak akses pengguna</p>
                            </div>
                            <button type="button" wire:click="closeEdit" class="text-brand-300 hover:text-white transition">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                                <input type="text" wire:model="name" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                                @error('name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Username</label>
                                    <input type="text" wire:model="username" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                                    @error('username') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Email</label>
                                    <input type="email" wire:model="email" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                                    @error('email') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Role / Peran</label>
                                    <select wire:model.live="role" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                                        <option value="siswa">Siswa</option>
                                        <option value="kurir">Kurir</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    @error('role') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                @if($role === 'siswa')
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tingkat Kelas</label>
                                        <select wire:model="kelas" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                                            <option value="">-- Pilih Kelas --</option>
                                            <option value="10">Kelas 10</option>
                                            <option value="11">Kelas 11</option>
                                            <option value="12">Kelas 12</option>
                                        </select>
                                        @error('kelas') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jurusan SMKN 6 Jember</label>
                                        <select wire:model="jurusan" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 bg-white">
                                            @foreach(config('school.jurusan', []) as $code => $label)
                                                <option value="{{ $code }}">{{ $code }} - {{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('jurusan') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Password Baru (Kosongkan jika tidak diganti)</label>
                                <input type="password" wire:model="new_password" placeholder="Minimal 8 karakter" class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                                @error('new_password') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center gap-2 pt-2">
                                <input type="checkbox" id="is_active" wire:model="is_active" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 size-4" />
                                <label for="is_active" class="text-xs font-semibold text-gray-700">Akun Aktif (Dapat Login)</label>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-2 border-t border-gray-100">
                            <button type="button" wire:click="closeEdit" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-100 transition">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700 transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
