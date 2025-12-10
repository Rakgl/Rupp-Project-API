<?php

namespace App\Http\Requests\Api\V1\Admin\Car;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model_id' => 'sometimes|required|exists:models,id',
            'body_type_id' => 'nullable|exists:body_types,id',
            'stock_quantity' => 'sometimes|required|integer|min:0',
            'status' => 'nullable|string|max:255',
            'year' => 'sometimes|required|digits:4',
            'price' => 'nullable|numeric|min:0',
            'seat' => 'nullable|integer|min:1',
            'engine' => 'nullable|string|max:255',
            'door' => 'nullable|integer|min:1',
            'fuel_type' => 'sometimes|required|string|max:255',
            'condition' => 'sometimes|required|string|max:255',
            'transmission' => 'sometimes|required|string|max:255',
            'lease_price_per_month' => 'nullable|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'primary_image_index' => 'nullable|integer|min:0',
            'primary_image_id' => 'nullable|exists:car_images,id',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'exists:car_images,id',
        ];
    }
}
