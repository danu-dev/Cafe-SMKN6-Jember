<?php

namespace App\Livewire\Admin;

use App\Models\KategoriMenu;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class KategoriIndex extends Component
{
    use WithFileUploads;

    public string $nama = '';
    public string $icon = '';
    public $gambar;
    public ?string $existingGambar = null;
    public ?int $editingId = null;
    public bool $modalOpen = false;

    public array $availableIcons = [
        'utensils' => '🍴 Makanan',
        'cup-hot' => '☕ Minuman Hangat',
        'wine' => '🥤 Minuman Dingin',
        'cookie' => '🍪 Cemilan / Snack',
        'cake' => '🍰 Dessert / Kue',
        'flame' => '🔥 Populer / Pedas',
        'burger' => '🍔 Fast Food',
        'ice-cream' => '🍨 Es Krim',
        'sparkles' => '✨ Spesial',
    ];

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:50|unique:kategori_menus,nama,' . $this->editingId,
            'icon' => 'nullable|string|max:50',
            'gambar' => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'nama.required' => 'Nama kategori wajib diisi.',
        'nama.unique' => 'Nama kategori sudah digunakan.',
        'gambar.image' => 'File harus berupa gambar (JPG, PNG).',
        'gambar.max' => 'Ukuran gambar maksimal 2MB.',
    ];

    public function openCreate(): void
    {
        $this->reset(['nama', 'icon', 'gambar', 'existingGambar', 'editingId']);
        $this->resetValidation();
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $kategori = KategoriMenu::findOrFail($id);
        $this->editingId = $kategori->id;
        $this->nama = $kategori->nama;
        $this->icon = $kategori->icon ?? '';
        $this->existingGambar = $kategori->gambar;
        $this->gambar = null;
        $this->resetValidation();
        $this->modalOpen = true;
    }

    public function removeGambar(): void
    {
        if ($this->editingId) {
            $kategori = KategoriMenu::findOrFail($this->editingId);
            if ($kategori->gambar && Storage::disk('public')->exists($kategori->gambar)) {
                Storage::disk('public')->delete($kategori->gambar);
            }
            $kategori->update(['gambar' => null]);
            $this->existingGambar = null;
        }
        $this->gambar = null;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'nama' => $this->nama,
            'slug' => Str::slug($this->nama),
            'icon' => $this->icon ?: null,
        ];

        if ($this->gambar) {
            $data['gambar'] = $this->gambar->store('kategori', 'public');
        }

        if ($this->editingId) {
            $kategori = KategoriMenu::findOrFail($this->editingId);

            if ($this->gambar && $kategori->gambar && Storage::disk('public')->exists($kategori->gambar)) {
                Storage::disk('public')->delete($kategori->gambar);
            }

            $kategori->update($data);
            session()->flash('message', 'Kategori berhasil diperbarui.');
        } else {
            KategoriMenu::create($data);
            session()->flash('message', 'Kategori berhasil ditambahkan.');
        }

        $this->modalOpen = false;
        $this->reset(['nama', 'icon', 'gambar', 'existingGambar', 'editingId']);
    }

    public function delete(int $id): void
    {
        $kategori = KategoriMenu::withCount('menus')->findOrFail($id);

        if ($kategori->menus_count > 0) {
            session()->flash('error', 'Kategori tidak dapat dihapus karena masih memiliki menu.');
            return;
        }

        if ($kategori->gambar && Storage::disk('public')->exists($kategori->gambar)) {
            Storage::disk('public')->delete($kategori->gambar);
        }

        $kategori->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.kategori-index', [
            'categories' => KategoriMenu::withCount('menus')->latest()->get(),
        ]);
    }
}

