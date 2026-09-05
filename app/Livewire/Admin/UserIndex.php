<?php

namespace App\Livewire\Admin;

use App\Models\SaldoTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class UserIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $roleFilter = '';

    #[Url]
    public string $kelasFilter = '';

    // Modal Edit User
    public bool $showEditModal = false;
    public ?int $editingUserId = null;
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $role = 'siswa';
    public ?string $kelas = null;
    public ?string $jurusan = 'RPL';
    public bool $is_active = true;
    public string $new_password = '';

    // Modal Topup Saldo
    public bool $showTopupModal = false;
    public ?User $topupUser = null;
    public string $topupAmount = '';
    public string $topupKeterangan = '';

    // Modal Riwayat Saldo
    public bool $showRiwayatModal = false;
    public ?User $selectedUserForRiwayat = null;

    public function resetFilter(): void
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->kelasFilter = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter($value): void
    {
        if ($value !== 'siswa') {
            $this->kelasFilter = '';
        }
        $this->resetPage();
    }

    public function updatingKelasFilter(): void
    {
        $this->resetPage();
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username ?? '';
        $this->email = $user->email;
        $this->role = $user->role;
        $this->kelas = $user->kelas;
        $this->jurusan = $user->jurusan ?? 'RPL';
        $this->is_active = (bool) $user->is_active;
        $this->new_password = '';

        $this->showEditModal = true;
    }

    public function closeEdit(): void
    {
        $this->showEditModal = false;
        $this->editingUserId = null;
        $this->resetValidation();
    }

    public function saveUser(): void
    {
        $user = User::findOrFail($this->editingUserId);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:admin,kurir,siswa'],
            'kelas' => ['nullable', 'in:10,11,12'],
            'is_active' => ['boolean'],
            'new_password' => ['nullable', 'string', 'min:8'],
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'kelas' => $this->role === 'siswa' ? $this->kelas : null,
            'jurusan' => $this->role === 'siswa' ? $this->jurusan : null,
            'is_active' => $this->is_active,
        ];

        if (! empty($this->new_password)) {
            $data['password'] = Hash::make($this->new_password);
        }

        $user->update($data);
        $this->closeEdit();
        session()->flash('message', "Data user {$user->name} berhasil diperbarui.");
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "User {$user->name} berhasil {$status}.");
    }

    public function openTopup(int $id): void
    {
        $this->topupUser = User::where('role', 'siswa')->findOrFail($id);
        $this->topupAmount = '';
        $this->topupKeterangan = 'Topup saldo oleh admin/kasir';
        $this->showTopupModal = true;
    }

    public function closeTopup(): void
    {
        $this->showTopupModal = false;
        $this->topupUser = null;
        $this->topupAmount = '';
        $this->resetValidation();
    }

    public function processTopup(): void
    {
        $this->validate([
            'topupAmount' => ['required', 'numeric', 'min:1000', 'max:10000000'],
            'topupKeterangan' => ['nullable', 'string', 'max:255'],
        ]);

        if (! $this->topupUser) {
            return;
        }

        $amount = (float) $this->topupAmount;

        DB::transaction(function () use ($amount) {
            $user = User::lockForUpdate()->findOrFail($this->topupUser->id);
            $saldoSebelum = (float) $user->saldo;
            $saldoSesudah = $saldoSebelum + $amount;

            $user->update(['saldo' => $saldoSesudah]);

            SaldoTransaction::create([
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'tipe' => 'topup',
                'jumlah' => $amount,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'keterangan' => $this->topupKeterangan ?: 'Topup saldo oleh admin/kasir',
            ]);

            // Kirim notifikasi ke Siswa
            $user->notify(new \App\Notifications\CafeNotification(
                title: 'Saldo Berhasil Ditambahkan',
                message: "Saldo akun Anda telah diisi sebesar Rp " . number_format($amount, 0, ',', '.') . " oleh kasir/admin. Saldo saat ini: Rp " . number_format($saldoSesudah, 0, ',', '.'),
                type: 'balance',
                actionUrl: route('siswa.saldo.index')
            ));
        });

        $userName = $this->topupUser->name;
        $this->closeTopup();
        session()->flash('message', "Topup sebesar Rp " . number_format($amount, 0, ',', '.') . " untuk {$userName} berhasil!");
    }

    public function openRiwayat(int $id): void
    {
        $this->selectedUserForRiwayat = User::with(['saldoTransactions' => fn ($q) => $q->latest()->take(20)])->findOrFail($id);
        $this->showRiwayatModal = true;
    }

    public function closeRiwayat(): void
    {
        $this->showRiwayatModal = false;
        $this->selectedUserForRiwayat = null;
    }

    public function render()
    {
        $users = User::when($this->search, function ($q) {
            $q->where(function ($sub) {
                $sub->where('name', 'like', "%{$this->search}%")
                    ->orWhere('username', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        })
            ->when($this->roleFilter, fn ($q) => $q->where('role', $this->roleFilter))
            ->when($this->kelasFilter, fn ($q) => $q->where('kelas', $this->kelasFilter))
            ->latest()
            ->paginate(10);

        $counts = [
            'total' => User::count(),
            'siswa' => User::where('role', 'siswa')->count(),
            'kurir' => User::where('role', 'kurir')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        return view('livewire.admin.user-index', [
            'users' => $users,
            'counts' => $counts,
        ]);
    }
}
