<?php

namespace App\Http\Requests\Api\V1\Admin\UserListing;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'model_id' => 'required|exists:models,id',
            'year' => 'required|digits:4|integer|min:1900',
            'condition' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string|min:20|max:5000',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'primary_image_index' => 'nullable|integer|min:0',
            'primary_image_id' => 'nullable|exists:user_listing_images,id',
            'remove_image_ids' => 'nullable|array',
            'remove_image_ids.*' => 'exists:user_listing_images,id',
        ];
    }
}