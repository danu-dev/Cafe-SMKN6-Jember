<?php

namespace App\Livewire\Admin;

use App\Models\KategoriMenu;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class KategoriIndex extends Component
{
    public string $nama = '';
    public ?int $editingId = null;
    public bool $modalOpen = false;

    protected function rules(): array
    {
        return [
            'nama' => 'required|string|max:50|unique:kategori_menus,nama,' . $this->editingId,
        ];
    }

    protected $messages = [
        'nama.required' => 'Nama kategori wajib diisi.',
        'nama.unique' => 'Nama kategori sudah digunakan.',
    ];

    public function openCreate(): void
    {
        $this->reset(['nama', 'editingId']);
        $this->modalOpen = true;
    }

    public function openEdit(int $id): void
    {
        $kategori = KategoriMenu::findOrFail($id);
        $this->editingId = $kategori->id;
        $this->nama = $kategori->nama;
        $this->modalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            $kategori = KategoriMenu::findOrFail($this->editingId);
            $kategori->update([
                'nama' => $this->nama,
                'slug' => Str::slug($this->nama),
            ]);
            session()->flash('message', 'Kategori berhasil diperbarui.');
        } else {
            KategoriMenu::create([
                'nama' => $this->nama,
                'slug' => Str::slug($this->nama),
            ]);
            session()->flash('message', 'Kategori berhasil ditambahkan.');
        }

        $this->modalOpen = false;
        $this->reset(['nama', 'editingId']);
    }

    public function delete(int $id): void
    {
        $kategori = KategoriMenu::withCount('menus')->findOrFail($id);

        if ($kategori->menus_count > 0) {
            session()->flash('error', 'Kategori tidak dapat dihapus karena masih memiliki menu.');
            return;
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
