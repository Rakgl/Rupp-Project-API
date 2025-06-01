<?php

namespace App\Http\Requests\Api\V1\Admin\Security;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $password = [];
        if($this->password && !$this->user){
            $password = ['required', 'min:6'];
        }

        return [
            'name' => ['required', 'string', 'max:100'],
            'role_id' => 'required',
            'username' => [
                'required', 'string', 'max:20', 
                Rule::unique('users', 'username')
                    ->ignore($this->route('user') ?? $this->id, 'id')
                    ->where(function ($query) {
                        $query->where('status', '!=', 'DELETED');
                    }),
            ],
            'password' => $password,
            'image' => 'nullable',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Please fill in information are required.',
            'message_details' => $validator->errors()
        ], 400));
    }
}
