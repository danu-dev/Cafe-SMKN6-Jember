<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_pesanan' => $this->kode_pesanan,
            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'kelas' => $this->user?->kelas,
                'jurusan' => $this->user?->jurusan,
            ],
            'kurir' => [
                'id' => $this->kurir?->id,
                'name' => $this->kurir?->name,
            ],
            'tipe_pengiriman' => $this->tipe_pengiriman,
            'metode_pembayaran' => $this->metode_pembayaran,
            'status_pembayaran' => $this->status_pembayaran,
            'status' => $this->status,
            'total_harga' => (float) $this->total_harga,
            'catatan' => $this->catatan,
            'items' => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'menu_nama' => $item->menu?->nama,
                'jumlah' => $item->jumlah,
                'harga_satuan' => (float) $item->harga_satuan,
                'subtotal' => (float) $item->subtotal,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'paid_at' => $this->paid_at?->toISOString(),
        ];
    }
}
