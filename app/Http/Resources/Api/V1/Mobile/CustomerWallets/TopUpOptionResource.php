<?php

namespace App\Http\Resources\Api\V1\Mobile\CustomerWallets;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopUpOptionResource extends JsonResource
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
			'amount' => number_format($this->amount, 0, '.', ''),
			'status' => $this->status
		];
    }
}
