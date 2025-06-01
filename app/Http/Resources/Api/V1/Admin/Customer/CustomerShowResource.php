<?php

namespace App\Http\Resources\Api\V1\Admin\Customer;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$user = User::where('customer_id', $this->id)->first();
        return [
			'name' => $this->name,
			'country_code' => $this->country_code,
			'phone' => $this->phone,
			'email' => $this->email,
			'country_code' => $this->country_code,
			'status' => $this->status,
			'created_at' => Helper::formatDate($this->created_at),
			'balance' => number_format($this->balance, 2) . ' KHR',
			'points' => number_format($this->points, 2) . ' Points',
			'membership' => $this->membership?->name,
			'fcm_token' => $user?->fcm_token
		];
    }
}
