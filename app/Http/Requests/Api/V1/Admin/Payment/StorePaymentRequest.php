<?php

namespace App\Http\Requests\Api\V1\Admin\Payment;

use App\Models\CarSale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $allowedPayables = [CarSale::class];

        return [
            'user_id' => 'required|exists:users,id',
            'payable_type' => ['required', Rule::in($allowedPayables)],
            'payable_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $type = $this->input('payable_type');
                    if ($type === CarSale::class && !CarSale::where('id', $value)->exists()) {
                        $fail('The selected payable item is invalid.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:0',
            'method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255|unique:payments,transaction_id',
            'status' => 'nullable|in:pending,success,failed,refunded',
        ];
    }
}
