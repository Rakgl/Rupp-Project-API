<?php

namespace App\Http\Requests\Api\V1\Admin\Station;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChargePointRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'station_id' => ['required', 'uuid', 'exists:stations,id'],
            'serial_number'         => ['nullable', 'string', 'max:255'],
            'factory_serial_number' => ['nullable', 'string', 'max:255'],
            'rate_khr_per_kwh' => ['required', 'numeric', 'min:0'],
            'min_start_balance' => ['required', 'numeric', 'min:0'],
            'max_charging_power' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Please fill in information are required.',
            'message_details' => $validator->errors()
        ], 400));
    }

}
