<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected string $secretKey;
    protected string $baseUrl;
    protected int $invoiceDuration;

    public function __construct()
    {
        $this->secretKey = config('xendit.secret_key', '');
        $this->baseUrl = config('xendit.base_url', 'https://api.xendit.co');
        $this->invoiceDuration = config('xendit.invoice_duration', 86400);
    }

    /**
     * Membuat invoice pembayaran Xendit.
     *
     * @param string $externalId ID unik transaksi (e.g. kode_pesanan atau topup-id)
     * @param float $amount Nominal dalam IDR
     * @param string $payerEmail Email pembayar
     * @param string $description Deskripsi transaksi
     * @param array $customer Data customer (name, email, phone)
     * @param array $items Daftar item pesanan
     * @param string|null $successRedirectUrl URL redirect setelah pembayaran sukses
     * @return array|null Response data invoice dari Xendit
     */
    public function createInvoice(
        string $externalId,
        float $amount,
        string $payerEmail,
        string $description,
        array $customer = [],
        array $items = [],
        ?string $successRedirectUrl = null
    ): ?array {
        $payload = [
            'external_id' => $externalId,
            'amount' => (int) $amount,
            'payer_email' => $payerEmail,
            'description' => $description,
            'invoice_duration' => $this->invoiceDuration,
            'currency' => 'IDR',
        ];

        if (! empty($customer)) {
            $payload['customer'] = $customer;
        }

        if (! empty($items)) {
            $payload['items'] = $items;
        }

        if ($successRedirectUrl) {
            $payload['success_redirect_url'] = $successRedirectUrl;
            $payload['failure_redirect_url'] = $successRedirectUrl;
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/v2/invoices", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Xendit Create Invoice Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Xendit API Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Mendapatkan status invoice dari Xendit berdasarkan invoice ID.
     */
    public function getInvoice(string $invoiceId): ?array
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get("{$this->baseUrl}/v2/invoices/{$invoiceId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('Xendit Get Invoice Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Memverifikasi webhook token callback dari Xendit.
     */
    public function verifyWebhookToken(?string $incomingToken): bool
    {
        $expectedToken = config('xendit.webhook_token');

        if (empty($expectedToken) || empty($incomingToken)) {
            return app()->environment('local', 'testing') && empty($expectedToken);
        }

        return hash_equals($expectedToken, $incomingToken);
    }
}
