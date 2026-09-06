<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Verifikasi Kartu Pelajar') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Periksa dan setujui kartu pelajar siswa agar dapat mengaktifkan metode pembayaran saldo online.') }}
            </p>
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

    {{-- Status Tabs --}}
    <div class="flex flex-wrap gap-2">
        <button
            wire:click="$set('statusFilter', 'pending')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Menunggu Verifikasi ({{ $counts['pending'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'verified')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'verified' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Disetujui ({{ $counts['verified'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'unverified')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'unverified' ? 'bg-gray-600 text-white shadow-sm shadow-gray-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Belum Upload ({{ $counts['unverified'] }})
        </button>
        <button
            wire:click="$set('statusFilter', 'all')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $statusFilter === 'all' ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Semua Siswa ({{ $counts['all'] }})
        </button>
    </div>

    {{-- Search Bar --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
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
    </div>

    {{-- Students Verification Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" wire:poll.10s>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Foto Kartu</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Verifikasi</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            {{-- Siswa Info --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-400">
                                            {{ $user->username ? '@'.$user->username : '-' }} • {{ $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Kelas --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-gray-700">
                                @if($user->kelas)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-brand-50 text-brand-800">
                                        Kelas {{ $user->kelas }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Foto Kartu Preview --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($user->kartu_pelajar_photo)
                                    <button wire:click="openDetail({{ $user->id }})" class="group inline-block">
                                        <img src="{{ asset('storage/' . $user->kartu_pelajar_photo) }}" alt="Kartu Pelajar" class="h-10 w-16 object-cover rounded-lg border border-gray-200 group-hover:scale-105 transition shadow-2xs inline" />
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum Upload</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($user->is_verified)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="size-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Terverifikasi
                                    </span>
                                @elseif($user->kartu_pelajar_photo)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        <span class="size-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                        Menunggu Persetujuan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                                        Belum Diverifikasi
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-1">
                                @if($user->kartu_pelajar_photo && ! $user->is_verified)
                                    <button
                                        wire:click="approve({{ $user->id }})"
                                        class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition"
                                    >
                                        Setujui
                                    </button>
                                @endif

                                @if($user->is_verified)
                                    <button
                                        wire:click="revoke({{ $user->id }})"
                                        wire:confirm="Yakin ingin mencabut status verifikasi siswa ini? Siswa tidak akan bisa bayar online."
                                        class="text-xs font-semibold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-2.5 py-1.5 rounded-lg transition"
                                    >
                                        Cabut
                                    </button>
                                @endif

                                <button
                                    wire:click="openDetail({{ $user->id }})"
                                    class="text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition"
                                >
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">
                                Tidak ada data siswa yang sesuai filter.
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

    {{-- Detail & Review Modal --}}
    @if($showDetailModal && $selectedUser)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeDetail"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-brand-900 px-6 py-5 text-white flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold">Verifikasi Kartu Pelajar</h3>
                            <p class="text-xs text-brand-200 mt-0.5">{{ $selectedUser->name }} (Kelas {{ $selectedUser->kelas ?? '-' }})</p>
                        </div>
                        <button wire:click="closeDetail" class="text-brand-300 hover:text-white transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Data Siswa --}}
                        <div class="bg-brand-50/60 p-4 rounded-xl border border-brand-100 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Nama Siswa:</span>
                                <span class="font-bold text-gray-900">{{ $selectedUser->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Username:</span>
                                <span class="font-bold text-gray-900">{{ $selectedUser->username ? '@'.$selectedUser->username : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Email:</span>
                                <span class="font-bold text-gray-900">{{ $selectedUser->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Kelas:</span>
                                <span class="font-bold text-brand-700">Kelas {{ $selectedUser->kelas ?? '-' }}</span>
                            </div>
                            @if($selectedUser->verified_at)
                                <div class="flex justify-between pt-1 border-t border-brand-100 text-emerald-700 font-medium">
                                    <span>Disetujui pada:</span>
                                    <span>{{ $selectedUser->verified_at->format('d M Y, H:i') }}</span>
                                </div>
                            @endif
                            @if($selectedUser->verification_note)
                                <div class="pt-1 border-t border-brand-100 text-rose-700">
                                    <strong>Catatan Terakhir:</strong> {{ $selectedUser->verification_note }}
                                </div>
                            @endif
                        </div>

                        {{-- Foto Kartu Pelajar --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-2">Foto Kartu Pelajar</label>
                            @if($selectedUser->kartu_pelajar_photo)
                                <div class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
                                    <img src="{{ asset('storage/' . $selectedUser->kartu_pelajar_photo) }}" alt="Foto Kartu Pelajar" class="w-full max-h-72 object-contain mx-auto" />
                                </div>
                            @else
                                <div class="p-8 bg-gray-50 rounded-xl border border-dashed border-gray-200 text-center text-xs text-gray-400">
                                    Siswa belum mengunggah foto kartu pelajar.
                                </div>
                            @endif
                        </div>

                        {{-- Form Alasan Tolak (jika ingin reject) --}}
                        @if($selectedUser->kartu_pelajar_photo && ! $selectedUser->is_verified)
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Alasan Penolakan (Jika Ditolak)</label>
                                <input
                                    type="text"
                                    wire:model="rejectNote"
                                    placeholder="Contoh: Foto buram, nama tidak sesuai, dll..."
                                    class="block w-full border border-gray-200 rounded-lg text-sm px-3 py-2 focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500"
                                />
                                @error('rejectNote') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-t border-gray-100">
                        <button
                            wire:click="closeDetail"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-100 transition"
                        >
                            Tutup
                        </button>

                        <div class="space-x-2">
                            @if($selectedUser->kartu_pelajar_photo && ! $selectedUser->is_verified)
                                <button
                                    wire:click="reject({{ $selectedUser->id }})"
                                    class="px-4 py-2 bg-rose-600 text-white rounded-lg text-xs font-semibold hover:bg-rose-700 transition"
                                >
                                    Tolak Kartu
                                </button>
                                <button
                                    wire:click="approve({{ $selectedUser->id }})"
                                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 shadow-xs transition"
                                >
                                    Setujui & Aktifkan Saldo
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
