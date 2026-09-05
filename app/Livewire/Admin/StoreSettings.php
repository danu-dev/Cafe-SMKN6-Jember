<?php

namespace App\Livewire\Admin;

use App\Models\StoreSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class StoreSettings extends Component
{
    public string $store_name = '';
    public string $store_description = '';
    public string $whatsapp_contact = '';
    public bool $auto_accept_orders = false;

    public function mount(): void
    {
        $settings = StoreSetting::allKeyValues();

        $this->store_name = $settings['store_name'] ?? 'Cafe Kantin Sekolah';
        $this->store_description = $settings['store_description'] ?? '';
        $this->whatsapp_contact = $settings['whatsapp_contact'] ?? '';
        $this->auto_accept_orders = ($settings['auto_accept_orders'] ?? '0') === '1';
    }

    public function save(): void
    {
        $this->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'store_description' => ['nullable', 'string', 'max:500'],
            'whatsapp_contact' => ['nullable', 'string', 'max:20'],
        ]);

        StoreSetting::set('store_name', $this->store_name);
        StoreSetting::set('store_description', $this->store_description);
        StoreSetting::set('whatsapp_contact', $this->whatsapp_contact);
        StoreSetting::set('auto_accept_orders', $this->auto_accept_orders ? '1' : '0');

        session()->flash('message', 'Pengaturan toko berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.admin.store-settings');
    }
}
