<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class XenditWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'external_id' => ['required', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:50'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_channel' => ['nullable', 'string', 'max:50'],
            'amount' => ['nullable', 'numeric'],
            'paid_amount' => ['nullable', 'numeric'],
        ];
    }
}
