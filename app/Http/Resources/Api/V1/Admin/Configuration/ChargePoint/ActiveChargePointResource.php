<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\ChargePoint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActiveChargePointResource extends JsonResource
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
			'name' => $this->serial_number,
			'number_of_connectors' => $this->number_of_connectors
		];
    }
}
