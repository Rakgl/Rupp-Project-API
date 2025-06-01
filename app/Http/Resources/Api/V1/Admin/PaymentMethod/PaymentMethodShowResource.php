<?php

namespace App\Http\Resources\Api\V1\Admin\PaymentMethod;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		return [
			'name' => $this->name,
			'description' => $this->description,
            'image' => Helper::imageUrl($this->image),
			'type' => $this->type,
			'status' => $this->status
		];
    }
}
