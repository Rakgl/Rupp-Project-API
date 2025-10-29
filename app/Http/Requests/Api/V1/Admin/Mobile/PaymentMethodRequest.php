<?php

// FILE: app/Http/Requests/Api/V1/Admin/Mobile/PaymentMethodRequest.php
// UPDATED: The validation rules are now more robust.

namespace App\Http\Requests\Api\V1\Admin\Mobile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
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
        // Get the payment method ID from the route for the uniqueness check.
        // For a 'store' request, $this->route('payment_method') will be null.
        $paymentMethodId = $this->route('payment_method');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('payment_methods')
                    ->ignore($paymentMethodId)
                    ->whereNull('deleted_at'), // <-- ADD THIS LINE
            ],
            'description' => 'nullable|string|max:1000',
            'type' => 'required|string|in:online,cash,card_on_delivery',
            'status' => [
                Rule::requiredIf($this->isMethod('put') || $this->isMethod('patch')),
                'string',
                'in:ACTIVE,INACTIVE',
            ],
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'The payment method name has already been taken.',
            'name.required' => 'Payment method name is required.',
            'type.required' => 'Payment type is required.',
            'status.required' => 'Status is required.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed. Please check the form for errors.',
            'errors' => $validator->errors()
        ], 422)); // Using 422 Unprocessable Entity is more standard for validation errors.
    }
}