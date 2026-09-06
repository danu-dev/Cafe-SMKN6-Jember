<?php

namespace App\Livewire\Siswa;

use App\Models\SaldoTransaction;
use App\Models\TopupRequest;
use App\Models\User;
use App\Services\XenditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.siswa')]
class SaldoIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url]
    public string $tipeFilter = '';

    // Upload kartu pelajar
    public $kartuFoto;
    public bool $showUploadModal = false;

    // Topup Mandiri via Xendit
    public bool $showTopupModal = false;
    public string $nominalTopup = '';

    public function updatingTipeFilter(): void
    {
        $this->resetPage();
    }

    public function openUploadModal(): void
    {
        $this->kartuFoto = null;
        $this->resetValidation();
        $this->showUploadModal = true;
    }

    public function closeUploadModal(): void
    {
        $this->showUploadModal = false;
        $this->kartuFoto = null;
        $this->resetValidation();
    }

    public function uploadKartu(): void
    {
        $this->validate([
            'kartuFoto' => ['required', 'image', 'max:3072'], // Max 3MB
        ], [
            'kartuFoto.required' => 'Foto kartu pelajar wajib dipilih.',
            'kartuFoto.image' => 'File harus berupa gambar (JPG, PNG).',
            'kartuFoto.max' => 'Ukuran foto maksimal 3MB.',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Delete old photo if exists
        if ($user->kartu_pelajar_photo && Storage::disk('public')->exists($user->kartu_pelajar_photo)) {
            Storage::disk('public')->delete($user->kartu_pelajar_photo);
        }

        $path = $this->kartuFoto->store('kartu-pelajar', 'public');

        $user->update([
            'kartu_pelajar_photo' => $path,
            'is_verified' => false,
            'verification_note' => null,
        ]);

        // Notif ke Admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\CafeNotification(
                title: 'Pengajuan Verifikasi Kartu Pelajar',
                message: "Siswa {$user->name} (" . ($user->kelas ? 'Kelas ' . $user->kelas : 'Siswa') . ") telah mengunggah kartu pelajar untuk diverifikasi.",
                type: 'verification',
                actionUrl: route('admin.verifikasi.index')
            ));
        }

        $this->closeUploadModal();
        session()->flash('message', 'Foto kartu pelajar berhasil diunggah. Mohon tunggu verifikasi dari pihak admin.');
    }

    public function openTopupModal(): void
    {
        $this->nominalTopup = '';
        $this->resetValidation();
        $this->showTopupModal = true;
    }

    public function closeTopupModal(): void
    {
        $this->showTopupModal = false;
        $this->nominalTopup = '';
        $this->resetValidation();
    }

    public function processTopupOnline(XenditService $xendit)
    {
        $this->validate([
            'nominalTopup' => ['required', 'numeric', 'min:5000', 'max:5000000'],
        ], [
            'nominalTopup.required' => 'Nominal top up wajib diisi.',
            'nominalTopup.min' => 'Nominal top up minimal Rp 5.000.',
            'nominalTopup.max' => 'Nominal top up maksimal Rp 5.000.000.',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $amount = (float) $this->nominalTopup;

        $externalId = 'TOPUP-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Create topup request record
        $topup = TopupRequest::create([
            'external_id' => $externalId,
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        // Create Xendit Invoice
        $successUrl = route('siswa.saldo.index');
        $invoice = $xendit->createInvoice(
            externalId: $externalId,
            amount: $amount,
            payerEmail: $user->email,
            description: "Top Up Saldo Cafe Siswa ({$user->name}) - Rp " . number_format($amount, 0, ',', '.'),
            customer: [
                'given_names' => $user->name,
                'email' => $user->email,
            ],
            items: [
                [
                    'name' => 'Top Up Saldo Cafe',
                    'quantity' => 1,
                    'price' => (int) $amount,
                    'category' => 'Topup',
                ],
            ],
            successRedirectUrl: $successUrl
        );

        if ($invoice && isset($invoice['invoice_url'])) {
            $topup->update([
                'xendit_invoice_id' => $invoice['id'],
                'xendit_payment_url' => $invoice['invoice_url'],
            ]);

            $this->closeTopupModal();

            // Redirect langsung ke URL pembayaran invoice Xendit
            return redirect()->away($invoice['invoice_url']);
        }

        $this->addError('nominalTopup', 'Gagal membuat invoice Xendit. Pastikan XENDIT_SECRET_KEY di file .env sudah diisi dengan API Key asli dari Dashboard Xendit.');
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();
        $userId = $user->id;

        $query = SaldoTransaction::where('user_id', $userId)
            ->when($this->tipeFilter, fn ($q) => $q->where('tipe', $this->tipeFilter))
            ->latest();

        $transactions = $query->paginate(10);

        // Stats
        $totalTopup = SaldoTransaction::where('user_id', $userId)->where('tipe', 'topup')->sum('jumlah');
        $totalPembayaran = SaldoTransaction::where('user_id', $userId)->where('tipe', 'pembayaran')->sum('jumlah');
        $totalRefund = SaldoTransaction::where('user_id', $userId)->where('tipe', 'refund')->sum('jumlah');

        $counts = [
            'all' => SaldoTransaction::where('user_id', $userId)->count(),
            'topup' => SaldoTransaction::where('user_id', $userId)->where('tipe', 'topup')->count(),
            'pembayaran' => SaldoTransaction::where('user_id', $userId)->where('tipe', 'pembayaran')->count(),
            'refund' => SaldoTransaction::where('user_id', $userId)->where('tipe', 'refund')->count(),
        ];

        return view('livewire.siswa.saldo-index', [
            'user' => $user,
            'saldo' => $user->saldo,
            'transactions' => $transactions,
            'totalTopup' => $totalTopup,
            'totalPembayaran' => $totalPembayaran,
            'totalRefund' => $totalRefund,
            'counts' => $counts,
        ]);
    }
}
