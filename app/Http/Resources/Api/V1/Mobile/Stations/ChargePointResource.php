<?php

namespace App\Http\Resources\Api\V1\Mobile\Stations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargePointResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'name' => $this->serial_number,
			'price' => number_format($this->rate_khr_per_kwh, 0, '.', ','),
			'power' => $this->max_charging_power,
			'type' => $this->type,
			'connectors' => ChargingConnectorResource::collection($this->chargingConnectors)
		];
    }
}
