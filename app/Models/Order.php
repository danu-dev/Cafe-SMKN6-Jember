<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'kode_pesanan',
        'user_id',
        'kurir_id',
        'tipe_pengiriman',
        'kelas_tujuan',
        'ruangan_tujuan',
        'jurusan_tujuan',
        'metode_pembayaran',
        'status_pembayaran',
        'xendit_invoice_id',
        'xendit_payment_url',
        'xendit_payment_channel',
        'paid_at',
        'status',
        'total_harga',
        'catatan',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kurir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kurir_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function saldoTransactions(): HasMany
    {
        return $this->hasMany(SaldoTransaction::class, 'order_id');
    }
}
