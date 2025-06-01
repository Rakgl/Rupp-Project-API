<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\ChargePoint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargePointConnectorResource extends JsonResource
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
			'number' => $this->connector_number,
			'status' => $this->status,
			'ocpp_status' => $this->ocpp_status
		];
    }
}
