<?php

namespace App\Http\Requests\Api\V1\Admin\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'model_id' => 'required|exists:models,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:5000',
        ];
    }
}
