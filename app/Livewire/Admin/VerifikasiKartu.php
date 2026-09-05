<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class VerifikasiKartu extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = 'pending'; // pending, verified, unverified, all

    public bool $showDetailModal = false;
    public ?User $selectedUser = null;
    public string $rejectNote = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openDetail(int $userId): void
    {
        $this->selectedUser = User::where('role', 'siswa')->findOrFail($userId);
        $this->rejectNote = '';
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedUser = null;
        $this->rejectNote = '';
    }

    public function approve(int $userId): void
    {
        $user = User::where('role', 'siswa')->findOrFail($userId);

        $user->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_note' => null,
        ]);

        $user->notify(new \App\Notifications\CafeNotification(
            title: 'Verifikasi Kartu Disetujui',
            message: 'Selamat! Kartu pelajar Anda telah diverifikasi oleh admin. Fitur pembayaran saldo online kini aktif.',
            type: 'verification',
            actionUrl: route('siswa.saldo.index')
        ));

        $this->closeDetail();
        session()->flash('message', "Kartu pelajar {$user->name} berhasil disetujui! Siswa kini dapat melakukan pembayaran online.");
    }

    public function reject(int $userId): void
    {
        $this->validate([
            'rejectNote' => ['required', 'string', 'max:255'],
        ], [
            'rejectNote.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $user = User::where('role', 'siswa')->findOrFail($userId);

        // Delete photo file if exists
        if ($user->kartu_pelajar_photo && Storage::disk('public')->exists($user->kartu_pelajar_photo)) {
            Storage::disk('public')->delete($user->kartu_pelajar_photo);
        }

        $user->update([
            'is_verified' => false,
            'kartu_pelajar_photo' => null,
            'verified_at' => null,
            'verification_note' => $this->rejectNote,
        ]);

        $user->notify(new \App\Notifications\CafeNotification(
            title: 'Verifikasi Kartu Ditolak',
            message: "Pengajuan kartu pelajar Anda ditolak: {$this->rejectNote}. Silakan unggah ulang foto kartu yang jelas.",
            type: 'verification',
            actionUrl: route('siswa.saldo.index')
        ));

        $this->closeDetail();
        session()->flash('message', "Verifikasi kartu {$user->name} ditolak. Alasan telah dicatat.");
    }

    public function revoke(int $userId): void
    {
        $user = User::where('role', 'siswa')->findOrFail($userId);

        $user->update([
            'is_verified' => false,
            'verified_at' => null,
            'verification_note' => 'Verifikasi dicabut oleh admin',
        ]);

        $this->closeDetail();
        session()->flash('message', "Status verifikasi {$user->name} berhasil dicabut.");
    }

    public function render()
    {
        $query = User::where('role', 'siswa')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('username', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, function ($q) {
                if ($this->statusFilter === 'pending') {
                    $q->where('is_verified', false)->whereNotNull('kartu_pelajar_photo');
                } elseif ($this->statusFilter === 'verified') {
                    $q->where('is_verified', true);
                } elseif ($this->statusFilter === 'unverified') {
                    $q->where('is_verified', false)->whereNull('kartu_pelajar_photo');
                }
            })
            ->latest();

        $users = $query->paginate(10);

        $counts = [
            'pending' => User::where('role', 'siswa')->where('is_verified', false)->whereNotNull('kartu_pelajar_photo')->count(),
            'verified' => User::where('role', 'siswa')->where('is_verified', true)->count(),
            'unverified' => User::where('role', 'siswa')->where('is_verified', false)->whereNull('kartu_pelajar_photo')->count(),
            'all' => User::where('role', 'siswa')->count(),
        ];

        return view('livewire.admin.verifikasi-kartu', [
            'users' => $users,
            'counts' => $counts,
        ]);
    }
}
