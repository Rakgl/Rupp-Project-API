<?php

namespace App\Http\Resources\Api\V1\Admin\CustomerPoint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerPointIndexResource extends JsonResource
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
			'name' => $this->name,
			'phone' => $this->phone,
			'points' => $this->points,
			'created_at' => date('d/m/Y , h:i A', strtotime($this->created_at)),
			'type' => $this->type,
			'reference_no' => $this->reference_no
		];
    }
}
