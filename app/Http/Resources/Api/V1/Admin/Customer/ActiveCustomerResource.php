<?php

namespace App\Http\Resources\Api\V1\Admin\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActiveCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'id' => $this->id,
			'name' => $this->name . '-'. $this->phone,
			'balance' => $this->balance,
			'point' => $this->points
		];
    }
}
