<?php

namespace App\Actions\Order;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SaldoTransaction;
use App\Models\StoreSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateOrderAction
{
    /**
     * Eksekusi pembuatan pesanan dan pemotongan saldo secara aman.
     *
     * @param User $user
     * @param string $tipePengiriman
     * @param string $metodePembayaran
     * @param array $validCart
     * @param float $totalHarga
     * @param string|null $catatan
     * @return Order
     * @throws InsufficientBalanceException
     */
    public function execute(
        User $user,
        string $tipePengiriman,
        string $metodePembayaran,
        array $validCart,
        float $totalHarga,
        ?string $catatan = null
    ): Order {
        $autoAccept = StoreSetting::get('auto_accept_orders', '0') === '1';
        $initialStatus = $autoAccept ? OrderStatus::DIPROSES->value : OrderStatus::MENUNGGU->value;
        $statusPembayaran = $metodePembayaran === PaymentMethod::SALDO->value
            ? PaymentStatus::SUDAH_DIBAYAR->value
            : PaymentStatus::BELUM_DIBAYAR->value;

        return DB::transaction(function () use (
            $user,
            $tipePengiriman,
            $metodePembayaran,
            $validCart,
            $totalHarga,
            $catatan,
            $initialStatus,
            $statusPembayaran
        ) {
            $lockedUser = User::lockForUpdate()->findOrFail($user->id);

            // Jika bayar saldo, potong saldo dengan race condition protection
            if ($metodePembayaran === PaymentMethod::SALDO->value) {
                if ((float) $lockedUser->saldo < $totalHarga) {
                    throw new InsufficientBalanceException('Saldo Anda tidak mencukupi untuk pembayaran pesanan ini.');
                }

                $saldoSebelum = (float) $lockedUser->saldo;
                $saldoSesudah = $saldoSebelum - $totalHarga;
                $lockedUser->update(['saldo' => $saldoSesudah]);
            }

            // Generate Kode Pesanan Unik
            $kodePesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            while (Order::where('kode_pesanan', $kodePesanan)->exists()) {
                $kodePesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }

            $order = Order::create([
                'kode_pesanan' => $kodePesanan,
                'user_id' => $lockedUser->id,
                'tipe_pengiriman' => $tipePengiriman,
                'kelas_tujuan' => $tipePengiriman === 'antar' ? $lockedUser->kelas : null,
                'jurusan_tujuan' => $tipePengiriman === 'antar' ? $lockedUser->jurusan : null,
                'ruangan_tujuan' => $tipePengiriman === 'antar' ? $lockedUser->ruangan : null,
                'metode_pembayaran' => $metodePembayaran,
                'status_pembayaran' => $statusPembayaran,
                'status' => $initialStatus,
                'total_harga' => $totalHarga,
                'catatan' => $catatan ?: null,
            ]);

            foreach ($validCart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['id'],
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            if ($metodePembayaran === PaymentMethod::SALDO->value) {
                SaldoTransaction::create([
                    'user_id' => $lockedUser->id,
                    'order_id' => $order->id,
                    'tipe' => 'pembayaran',
                    'jumlah' => $totalHarga,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'keterangan' => "Pembayaran Pesanan #{$order->kode_pesanan}",
                ]);
            }

            return $order;
        });
    }
}
