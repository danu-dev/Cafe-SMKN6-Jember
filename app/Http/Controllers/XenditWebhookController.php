<?php

namespace App\Http\Controllers;

use App\Http\Requests\XenditWebhookRequest;
use App\Models\Order;
use App\Models\SaldoTransaction;
use App\Models\TopupRequest;
use App\Models\User;
use App\Services\XenditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    /**
     * Handle incoming callback webhook from Xendit.
     */
    public function handle(XenditWebhookRequest $request, XenditService $xendit): JsonResponse
    {
        $callbackToken = $request->header('x-callback-token');

        if (! $xendit->verifyWebhookToken($callbackToken)) {
            Log::warning('Xendit Webhook Unauthorized Attempt', [
                'ip' => $request->ip(),
                'token' => $callbackToken,
            ]);

            return response()->json(['message' => 'Unauthorized token'], 401);
        }

        $validated = $request->validated();
        $externalId = $validated['external_id'];
        $status = strtoupper($validated['status']);
        $paymentMethod = $validated['payment_method'] ?? ($validated['payment_channel'] ?? null);
        $paidAmount = (float) ($validated['paid_amount'] ?? ($validated['amount'] ?? 0));

        Log::info('Xendit Webhook Received', [
            'external_id' => $externalId,
            'status' => $status,
            'payment_method' => $paymentMethod,
        ]);

        if (! $externalId) {
            return response()->json(['message' => 'Missing external_id'], 400);
        }

        // 1. Handle TOPUP Webhook
        if (str_starts_with($externalId, 'TOPUP-')) {
            return $this->handleTopupWebhook($externalId, $status, $paymentMethod, $paidAmount);
        }

        // 2. Handle ORDER Webhook
        if (str_starts_with($externalId, 'ORD-')) {
            return $this->handleOrderWebhook($externalId, $status, $paymentMethod);
        }

        return response()->json(['message' => 'Unhandled external_id format'], 200);
    }

    protected function handleTopupWebhook(string $externalId, string $status, ?string $paymentMethod, float $paidAmount): JsonResponse
    {
        if ($status === 'PAID' || $status === 'SETTLED') {
            $processed = DB::transaction(function () use ($externalId, $paymentMethod, $paidAmount) {
                $topup = TopupRequest::where('external_id', $externalId)->lockForUpdate()->first();

                if (! $topup) {
                    return 'not_found';
                }

                if ($topup->status === 'paid') {
                    return 'already_paid';
                }

                $user = User::lockForUpdate()->findOrFail($topup->user_id);
                $saldoSebelum = (float) $user->saldo;
                $amount = $paidAmount > 0 ? $paidAmount : (float) $topup->amount;
                $saldoSesudah = $saldoSebelum + $amount;

                $user->update(['saldo' => $saldoSesudah]);

                $topup->update([
                    'status' => 'paid',
                    'payment_channel' => $paymentMethod,
                    'paid_at' => now(),
                ]);

                SaldoTransaction::create([
                    'user_id' => $user->id,
                    'tipe' => 'topup',
                    'jumlah' => $amount,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'keterangan' => "Top Up Otomatis via Xendit ({$paymentMethod})",
                ]);

                // Notif ke Siswa
                $user->notify(new \App\Notifications\CafeNotification(
                    title: 'Top Up Online Berhasil',
                    message: "Top up via Xendit ({$paymentMethod}) sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil masuk ke saldo Anda!",
                    type: 'balance',
                    actionUrl: route('siswa.saldo.index')
                ));

                return 'success';
            });

            if ($processed === 'not_found') {
                Log::error("TopupRequest with external_id {$externalId} not found");
                return response()->json(['message' => 'Topup not found'], 404);
            }

            if ($processed === 'already_paid') {
                return response()->json(['message' => 'Topup already processed'], 200);
            }

            Log::info("Topup {$externalId} successfully paid and saldo incremented");
            return response()->json(['message' => 'Topup successfully processed'], 200);
        }

        if ($status === 'EXPIRED') {
            TopupRequest::where('external_id', $externalId)
                ->where('status', 'pending')
                ->update(['status' => 'expired']);

            return response()->json(['message' => 'Topup marked as expired'], 200);
        }

        return response()->json(['message' => 'Webhook status recorded'], 200);
    }

    protected function handleOrderWebhook(string $externalId, string $status, ?string $paymentMethod): JsonResponse
    {
        if ($status === 'PAID' || $status === 'SETTLED') {
            $result = DB::transaction(function () use ($externalId, $paymentMethod) {
                $order = Order::with('user')->where('kode_pesanan', $externalId)->lockForUpdate()->first();

                if (! $order) {
                    return 'not_found';
                }

                if ($order->status_pembayaran === 'sudah_dibayar') {
                    return 'already_paid';
                }

                $order->update([
                    'status_pembayaran' => 'sudah_dibayar',
                    'xendit_payment_channel' => $paymentMethod,
                    'paid_at' => now(),
                    'status' => $order->status === 'menunggu' ? 'diproses' : $order->status,
                ]);

                if ($order->user) {
                    $order->user->notify(new \App\Notifications\CafeNotification(
                        title: 'Pembayaran Diterima',
                        message: "Pembayaran untuk pesanan #{$order->kode_pesanan} telah lunas via {$paymentMethod}. Pesanan segera diproses.",
                        type: 'order',
                        actionUrl: route('siswa.orders.index')
                    ));
                }

                // Notif ke Admin
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\CafeNotification(
                        title: 'Pembayaran Masuk (Xendit)',
                        message: "Pesanan #{$order->kode_pesanan} ({$order->user->name}) telah dibayar via {$paymentMethod}.",
                        type: 'order',
                        actionUrl: route('admin.orders.index')
                    ));
                }

                return 'success';
            });

            if ($result === 'not_found') {
                Log::error("Order with kode_pesanan {$externalId} not found");
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($result === 'already_paid') {
                return response()->json(['message' => 'Order already paid'], 200);
            }

            Log::info("Order {$externalId} successfully marked as PAID via Xendit ({$paymentMethod})");
            return response()->json(['message' => 'Order payment recorded'], 200);
        }

        if ($status === 'EXPIRED') {
            Order::where('kode_pesanan', $externalId)
                ->where('status', 'menunggu')
                ->where('status_pembayaran', 'belum_dibayar')
                ->update(['status' => 'dibatalkan']);

            return response()->json(['message' => 'Order marked as cancelled due to expired payment'], 200);
        }

        return response()->json(['message' => 'Order webhook recorded'], 200);
    }
}
