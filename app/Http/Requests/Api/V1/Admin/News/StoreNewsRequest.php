<?php

namespace App\Http\Requests\Api\V1\Admin\News;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Validate Name (JSON)
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.km' => 'nullable|string|max:255',

            // Validate Description (JSON)
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.km' => 'nullable|string',

            // Validate Image (Input name: 'image')
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            
            // Validate Status
            'status' => 'nullable|string|in:ACTIVE,INACTIVE,DELETED',
        ];
    }
}