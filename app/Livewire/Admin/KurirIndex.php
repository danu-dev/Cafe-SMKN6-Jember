<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class KurirIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    // Modal Form Create/Edit
    public bool $showFormModal = false;
    public ?int $editingKurirId = null;
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public bool $is_active = true;

    // Modal Riwayat
    public bool $showRiwayatModal = false;
    public ?User $selectedKurir = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetForm();
        $kurir = User::where('role', 'kurir')->findOrFail($id);

        $this->editingKurirId = $kurir->id;
        $this->name = $kurir->name;
        $this->username = $kurir->username ?? '';
        $this->email = $kurir->email;
        $this->is_active = (bool) $kurir->is_active;

        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingKurirId = null;
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('users', 'username')->ignore($this->editingKurirId)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingKurirId)],
            'is_active' => ['boolean'],
        ];

        if (! $this->editingKurirId) {
            $rules['password'] = ['required', 'string', 'min:8'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8'];
        }

        $validated = $this->validate($rules);

        if ($this->editingKurirId) {
            $kurir = User::where('role', 'kurir')->findOrFail($this->editingKurirId);
            $data = [
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'is_active' => $this->is_active,
            ];
            if (! empty($this->password)) {
                $data['password'] = $this->password;
            }
            $kurir->update($data);
            session()->flash('message', 'Data kurir berhasil diperbarui.');
        } else {
            User::create([
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'password' => $this->password,
                'role' => 'kurir',
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Kurir baru berhasil ditambahkan.');
        }

        $this->closeFormModal();
    }

    public function toggleActive(int $id): void
    {
        $kurir = User::where('role', 'kurir')->findOrFail($id);
        $kurir->update(['is_active' => ! $kurir->is_active]);

        $status = $kurir->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('message', "Kurir {$kurir->name} berhasil {$status}.");
    }

    public function openRiwayat(int $id): void
    {
        $this->selectedKurir = User::with(['deliveries.user', 'deliveries.items.menu'])
            ->where('role', 'kurir')
            ->findOrFail($id);
        $this->showRiwayatModal = true;
    }

    public function closeRiwayat(): void
    {
        $this->showRiwayatModal = false;
        $this->selectedKurir = null;
    }

    public function render()
    {
        $today = Carbon::today();

        $kurirs = User::where('role', 'kurir')
            ->withCount([
                'deliveries as total_pengantaran',
                'deliveries as pengantaran_aktif' => fn ($q) => $q->whereIn('status', ['diproses', 'siap', 'diantar']),
                'deliveries as selesai_hari_ini' => fn ($q) => $q->where('status', 'selesai')->whereDate('updated_at', $today),
            ])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('username', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);

        // Stats
        $totalKurir = User::where('role', 'kurir')->count();
        $kurirAktif = User::where('role', 'kurir')->where('is_active', true)->count();
        $antarHariIni = Order::whereNotNull('kurir_id')->whereDate('created_at', $today)->count();
        $selesaiHariIni = Order::whereNotNull('kurir_id')->where('status', 'selesai')->whereDate('updated_at', $today)->count();

        return view('livewire.admin.kurir-index', [
            'kurirs' => $kurirs,
            'totalKurir' => $totalKurir,
            'kurirAktif' => $kurirAktif,
            'antarHariIni' => $antarHariIni,
            'selesaiHariIni' => $selesaiHariIni,
        ]);
    }
}
