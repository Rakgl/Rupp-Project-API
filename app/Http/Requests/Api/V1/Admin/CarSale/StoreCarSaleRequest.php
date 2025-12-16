<?php

namespace App\Http\Requests\Api\V1\Admin\CarSale;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarSaleRequest extends FormRequest
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
            'car_id' => 'required|exists:cars,id',
            'buyer_id' => 'required|exists:users,id',
            'final_price' => 'required|numeric|min:0',
            'status' => 'nullable|string|in:requested,approved,payment_pending,paid,completed,cancelled',
        ];
    }
}
