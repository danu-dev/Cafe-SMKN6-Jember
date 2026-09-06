<?php

namespace App\Livewire\Admin;

use App\Models\KategoriMenu;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class MenuIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public string $kategoriFilter = '';
    public string $statusFilter = '';

    // Form fields
    public bool $modalOpen = false;
    public ?int $editingId = null;
    public ?int $kategori_id = null;
    public string $nama = '';
    public string $deskripsi = '';
    public string $harga = '';
    public int $stok = 0;
    public bool $is_available = true;
    public $gambar;
    public ?string $existingGambar = null;

    protected function rules(): array
    {
        return [
            'kategori_id' => 'required|exists:kategori_menus,id',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'is_available' => 'boolean',
            'gambar' => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'kategori_id.required' => 'Kategori menu wajib dipilih.',
        'nama.required' => 'Nama menu wajib diisi.',
        'harga.required' => 'Harga wajib diisi.',
        'harga.numeric' => 'Harga harus berupa angka.',
        'stok.required' => 'Stok wajib diisi.',
        'gambar.image' => 'File harus berupa gambar.',
        'gambar.max' => 'Ukuran gambar maksimal 2MB.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingKategoriFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetValidation();
        $this->reset([
            'editingId', 'kategori_id', 'nama', 'deskripsi',
            'harga', 'stok', 'is_available', 'gambar', 'existingGambar',
        ]);
        $this->is_available = true;
        $this->stok = 10;
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $this->resetValidation();
        $menu = Menu::findOrFail($id);
        $this->editingId = $menu->id;
        $this->kategori_id = $menu->kategori_id;
        $this->nama = $menu->nama;
        $this->deskripsi = (string) $menu->deskripsi;
        $this->harga = (string) ((int) $menu->harga);
        $this->stok = $menu->stok;
        $this->is_available = (bool) $menu->is_available;
        $this->existingGambar = $menu->gambar;
        $this->gambar = null;
        $this->modalOpen = true;
    }

    public function toggleAvailability(int $id): void
    {
        $menu = Menu::findOrFail($id);
        $menu->update(['is_available' => ! $menu->is_available]);

        $status = $menu->is_available ? 'Ready' : 'Not Ready';
        session()->flash('message', "Status menu \"{$menu->nama}\" diubah menjadi {$status}.");
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'kategori_id' => $this->kategori_id,
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'harga' => $this->harga,
            'stok' => $this->stok,
            'is_available' => $this->is_available,
        ];

        if ($this->gambar) {
            $data['gambar'] = $this->gambar->store('menus', 'public');
        }

        if ($this->editingId) {
            $menu = Menu::findOrFail($this->editingId);

            // Delete old image if new one uploaded
            if ($this->gambar && $menu->gambar) {
                Storage::disk('public')->delete($menu->gambar);
            }

            $menu->update($data);
            session()->flash('message', 'Menu "' . $menu->nama . '" berhasil diperbarui.');
        } else {
            Menu::create($data);
            session()->flash('message', 'Menu baru berhasil ditambahkan.');
        }

        $this->modalOpen = false;
        $this->reset([
            'editingId', 'kategori_id', 'nama', 'deskripsi',
            'harga', 'is_available', 'gambar', 'existingGambar',
        ]);
    }

    public function delete(int $id): void
    {
        $menu = Menu::findOrFail($id);

        if ($menu->gambar) {
            Storage::disk('public')->delete($menu->gambar);
        }

        $menu->delete();
        session()->flash('message', 'Menu berhasil dihapus.');
    }

    public function render()
    {
        $query = Menu::with('kategori')
            ->when($this->search, function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
            })
            ->when($this->kategoriFilter, function ($q) {
                $q->where('kategori_id', $this->kategoriFilter);
            })
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('is_available', $this->statusFilter === '1');
            });

        return view('livewire.admin.menu-index', [
            'menus' => $query->latest()->paginate(8),
            'categories' => KategoriMenu::orderBy('nama')->get(),
        ]);
    }
}
