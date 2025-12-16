<?php

namespace App\Http\Requests\Api\V1\Admin\CarSale;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarSaleRequest extends FormRequest
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
        return [
            'car_id' => 'sometimes|exists:cars,id',
            'buyer_id' => 'sometimes|exists:users,id',
            'final_price' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:requested,approved,payment_pending,paid,completed,cancelled',
        ];
    }
}
