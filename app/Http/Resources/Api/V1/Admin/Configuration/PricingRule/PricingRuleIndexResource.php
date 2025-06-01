<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\PricingRule;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PricingRuleIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$chargingConnector  = $this->chargingConnector;
		$chargePoint = $chargingConnector->chargePoint;
		$station  = $chargePoint->station;

		return [	
			'station' => $station ? $station->name : null,
			'charge_point' => $chargePoint->model,
			'charging_connector' => $chargingConnector->type,
			'price_per_kwh' => $this->price_per_kwh,
			'status' => $this->status,
		];
    }
}
