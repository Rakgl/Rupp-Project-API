<?php

namespace App\Http\Resources\Api\V1\Admin\TopUpOption;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopUpOptionEditResource extends JsonResource
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
			'amount' => $this->amount,
			'status' => $this->status,
		];
    }
}
