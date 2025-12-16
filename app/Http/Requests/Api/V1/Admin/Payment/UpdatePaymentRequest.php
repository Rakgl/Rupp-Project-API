<?php

namespace App\Http\Requests\Api\V1\Admin\Payment;

use App\Models\CarSale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest
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
            'user_id' => 'sometimes|exists:users,id',
            'payable_type' => ['sometimes', Rule::in($allowedPayables)],
            'payable_id' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    $type = $this->input('payable_type');
                    if ($type === CarSale::class && !CarSale::where('id', $value)->exists()) {
                        $fail('The selected payable item is invalid.');
                    }
                },
            ],
            'amount' => 'sometimes|numeric|min:0',
            'method' => 'sometimes|nullable|string|max:255',
            'transaction_id' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('payments', 'transaction_id')->ignore($this->route('payment')?->id),
            ],
            'status' => 'sometimes|in:pending,success,failed,refunded',
        ];
    }
}
