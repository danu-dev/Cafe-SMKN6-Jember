<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                {{ __('Saldo Siswa') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Pantau sisa saldo dan seluruh riwayat mutasi transaksi Anda.') }}
            </p>
        </div>
        <div>
            <button
                wire:click="openTopupModal"
                class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-500/30 transition gap-2"
            >
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Top Up Saldo Online (QRIS/VA)
            </button>
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

    {{-- Verification Status Banner (Hanya muncul jika belum verified) --}}
    @if(! $user->is_verified)
        @if($user->kartu_pelajar_photo)
            <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold animate-pulse">
                        !
                    </div>
                    <div>
                        <div class="text-sm font-bold text-amber-950">Menunggu Persetujuan Admin</div>
                        <div class="text-xs text-amber-700">Foto kartu pelajar Anda sedang ditinjau. Pembayaran saldo akan aktif setelah disetujui.</div>
                    </div>
                </div>
                <button
                    wire:click="openUploadModal"
                    class="text-xs font-bold text-amber-800 bg-amber-100 hover:bg-amber-200 px-3 py-1.5 rounded-xl transition shrink-0"
                >
                    Ganti Foto Kartu
                </button>
            </div>
        @else
            <div class="p-5 bg-rose-50/70 border border-rose-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="size-10 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center font-bold shrink-0">
                        !
                    </div>
                    <div>
                        <div class="text-sm font-bold text-rose-950">Kartu Pelajar Belum Terverifikasi</div>
                        <div class="text-xs text-rose-700 mt-0.5">
                            Upload foto kartu pelajar Anda untuk mengaktifkan fitur pembayaran online (saldo).
                            @if($user->verification_note)
                                <div class="mt-1 font-semibold text-rose-800">Catatan sebelumnya: {{ $user->verification_note }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <button
                    wire:click="openUploadModal"
                    class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-xs transition shrink-0"
                >
                    Upload Kartu Pelajar
                </button>
            </div>
        @endif
    @endif

    {{-- Main Saldo Banner + Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        {{-- Card Besar Saldo --}}
        <div class="lg:col-span-2 bg-gradient-to-br from-brand-800 via-brand-700 to-brand-900 rounded-2xl p-6 text-white shadow-lg shadow-brand-950/20 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-brand-200">Saldo Cafe Aktif</span>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/20 backdrop-blur-xs">
                        {{ auth()->user()->username ? '@'.auth()->user()->username : 'Siswa' }}
                    </span>
                </div>
                <div class="text-3xl sm:text-4xl font-black mt-3">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-white/15 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-brand-200">
                <span>Top up mandiri via QRIS / Bank Transfer (Xendit) atau bayar di kasir.</span>
            </div>
        </div>

        {{-- Stat 1: Total Topup --}}
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase text-gray-400">Total Topup</span>
                    <div class="size-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                        +
                    </div>
                </div>
                <div class="text-xl font-black text-emerald-600 mt-2">
                    Rp {{ number_format($totalTopup, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-xs text-gray-400 mt-2">Setoran masuk</div>
        </div>

        {{-- Stat 2: Total Belanja --}}
        <div class="bg-white rounded-2xl p-5 border border-brand-100 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase text-gray-400">Total Terpakai</span>
                    <div class="size-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center font-bold text-xs">
                        -
                    </div>
                </div>
                <div class="text-xl font-black text-rose-600 mt-2">
                    Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-xs text-gray-400 mt-2">Pembelian menu</div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="flex flex-wrap gap-2">
        <button
            wire:click="$set('tipeFilter', '')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $tipeFilter === '' ? 'bg-brand-600 text-white shadow-sm shadow-brand-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Semua Riwayat ({{ $counts['all'] }})
        </button>
        <button
            wire:click="$set('tipeFilter', 'topup')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $tipeFilter === 'topup' ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Topup ({{ $counts['topup'] }})
        </button>
        <button
            wire:click="$set('tipeFilter', 'pembayaran')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $tipeFilter === 'pembayaran' ? 'bg-rose-600 text-white shadow-sm shadow-rose-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Pembayaran ({{ $counts['pembayaran'] }})
        </button>
        <button
            wire:click="$set('tipeFilter', 'refund')"
            class="px-3.5 py-2 text-xs font-semibold rounded-lg transition-all {{ $tipeFilter === 'refund' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/30' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}"
        >
            Refund ({{ $counts['refund'] }})
        </button>
    </div>

    {{-- Transaction History Table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" wire:poll.5s>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/75">
                    <tr>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis & Keterangan</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Perubahan Saldo</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <div class="font-bold text-gray-900">{{ $tx->created_at->format('d M Y') }}</div>
                                <div class="text-gray-400 mt-0.5">{{ $tx->created_at->format('H:i') }} WIB</div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @php
                                        $txBadges = [
                                            'topup' => 'bg-emerald-100 text-emerald-800',
                                            'pembayaran' => 'bg-rose-100 text-rose-800',
                                            'refund' => 'bg-blue-100 text-blue-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $txBadges[$tx->tipe] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $tx->tipe }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-700 mt-1">
                                    {{ $tx->keterangan ?? '-' }}
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs text-gray-500 font-mono">
                                Rp {{ number_format($tx->saldo_sebelum, 0, ',', '.') }} &rarr; <span class="font-bold text-gray-900">Rp {{ number_format($tx->saldo_sesudah, 0, ',', '.') }}</span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right font-bold text-sm {{ $tx->tipe === 'pembayaran' ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $tx->tipe === 'pembayaran' ? '-' : '+' }} Rp {{ number_format($tx->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-gray-500 text-sm">
                                <div class="size-14 rounded-2xl bg-brand-50 text-brand-600 mx-auto flex items-center justify-center mb-3">
                                    <svg class="size-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>
                                </div>
                                <p class="text-base font-bold text-gray-900">Belum ada riwayat transaksi saldo</p>
                                <p class="text-xs text-gray-400 mt-1">Seluruh mutasi topup dan belanja akan tercatat rapi di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    {{-- Top Up Online Modal (Xendit) --}}
    @if($showTopupModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeTopupModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <form wire:submit="processTopupOnline">
                        <div class="bg-gradient-to-r from-emerald-700 to-teal-700 px-6 py-5 text-white flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold">Top Up Saldo Online</h3>
                                <p class="text-xs text-emerald-100 mt-0.5">Bayar via QRIS, Virtual Account, atau E-Wallet (Xendit)</p>
                            </div>
                            <button type="button" wire:click="closeTopupModal" class="text-emerald-200 hover:text-white transition">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nominal Top Up (Rp)</label>
                                <input
                                    type="number"
                                    wire:model="nominalTopup"
                                    placeholder="Contoh: 25000"
                                    class="block w-full border border-gray-200 rounded-lg text-base font-black px-3 py-2.5 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                                />
                                @error('nominalTopup') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Preset Buttons --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1.5">Pilihan Cepat Nominal</label>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach([10000, 20000, 50000, 100000, 150000, 200000] as $preset)
                                        <button
                                            type="button"
                                            wire:click="$set('nominalTopup', '{{ $preset }}')"
                                            class="py-2 px-2 text-xs font-bold rounded-xl border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50 transition text-gray-700"
                                        >
                                            Rp {{ number_format($preset, 0, ',', '.') }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Payment Channels Supported info --}}
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-1 text-gray-600">
                                <div class="font-bold text-gray-900 flex items-center gap-1.5">
                                    <span>Metode Pembayaran Didukung:</span>
                                </div>
                                <div class="text-[11px] text-gray-500">
                                    QRIS (GoPay, OVO, ShopeePay, DANA), Virtual Account BCA, BRI, BNI, Mandiri, Permata, serta Alfamart / Indomaret.
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-2 border-t border-gray-100">
                            <button type="button" wire:click="closeTopupModal" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-100 transition">
                                Batal
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="processTopupOnline">Lanjut ke Pembayaran &rarr;</span>
                                <span wire:loading wire:target="processTopupOnline">Membuat Invoice...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Upload Kartu Pelajar Modal --}}
    @if($showUploadModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" wire:click="closeUploadModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <form wire:submit="uploadKartu">
                        <div class="bg-brand-900 px-6 py-5 text-white flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold">Upload Kartu Pelajar</h3>
                                <p class="text-xs text-brand-200 mt-0.5">Unggah foto fisik kartu pelajar Anda yang jelas</p>
                            </div>
                            <button type="button" wire:click="closeUploadModal" class="text-brand-300 hover:text-white transition">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-2">Pilih File Foto (JPG / PNG, Maks 3MB)</label>
                                <input
                                    type="file"
                                    wire:model="kartuFoto"
                                    accept="image/*"
                                    class="w-full text-xs text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100"
                                />
                                @error('kartuFoto') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                                <div wire:loading wire:target="kartuFoto" class="text-xs text-brand-600 mt-2">
                                    Mengunggah foto kartu...
                                </div>

                                @if ($kartuFoto)
                                    <div class="mt-3">
                                        <div class="text-xs font-semibold text-gray-700 mb-1">Preview Kartu:</div>
                                        <img src="{{ $kartuFoto->temporaryUrl() }}" class="w-full max-h-48 object-contain rounded-xl border border-gray-200 bg-gray-50" />
                                    </div>
                                @endif
                            </div>

                            <div class="text-[11px] text-gray-500 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                💡 Pastikan nama dan NIS/kelas di kartu pelajar terbaca dengan jelas untuk mempercepat proses persetujuan oleh admin.
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-2 border-t border-gray-100">
                            <button type="button" wire:click="closeUploadModal" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-100 transition">
                                Batal
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700 transition disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="uploadKartu">Kirim Verifikasi</span>
                                <span wire:loading wire:target="uploadKartu">Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
