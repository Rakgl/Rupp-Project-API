<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\ChargePoint;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargePointShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			"id" => $this->id,
            "station" => $this->station ? $this->station->name : null,
			"vendor" => $this->vendor,
			"model" => $this->model,
			"serial_number" => $this->serial_number,
			"firmware_version" => $this->firmware_version,
			"rate_khr_per_kwh" => $this->rate_khr_per_kwh,
			"min_start_balance" => $this->min_start_balance,
			"max_charging_power" => $this->max_charging_power,
			"type" => $this->type,
			"status" => $this->status,
			// 'connectors' => ChargePointConnectorResource::collection($this->chargingConnectors),
		];
    }
}
