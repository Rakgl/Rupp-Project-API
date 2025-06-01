<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\ChargingConnector;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargingConnectorEditResource extends JsonResource
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
			'charge_point' => [
				'name' => $this->chargePoint ? $this->chargePoint->serial_number : null,
				'id' => $this->chargePoint ? $this->chargePoint->id : null
			],
			'connector_number' => $this->connector_number,
			'status' => $this->status,
		];
    }
}
