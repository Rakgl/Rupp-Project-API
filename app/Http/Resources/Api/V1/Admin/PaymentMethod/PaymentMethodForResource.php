<?php

namespace App\Http\Resources\Api\V1\Admin\PaymentMethod;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PaymentMethodForResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $type = "";
        if ($this->type == 'BANK') {
            $type = "BANK";
        } elseif ($this->type == 'E_WALLET') {
            $type = "E_WALLET";
        } else {
            $type = "CREDIT_CARD";
        }
		return [
			'id' => $this->id,
			'name' => $this->name,
			'description' => $this->description,
            'image' => $this->image,
			'type' => $this->$type,
			'status' => $this->status,
		];
    }
}

