<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaldoTransactionResource extends JsonResource
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
            'tipe' => $this->tipe,
            'jumlah' => (float) $this->jumlah,
            'saldo_sebelum' => (float) $this->saldo_sebelum,
            'saldo_sesudah' => (float) $this->saldo_sesudah,
            'keterangan' => $this->keterangan,
            'order_id' => $this->order_id,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
