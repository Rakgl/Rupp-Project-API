<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\ChargingConnector;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargingConnectorShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$chargePoint = $this->chargePoint;
        return [
			'id' => $this->id,
			'charge_point' => $chargePoint ? $this->chargePoint->serial_number : null,
			'last_active_at' =>  $chargePoint ? $chargePoint->last_active_at : null,
			'charge_point_model' => $this->charge_point_model,
			'connector_number' => $this->connector_number,
			'qr_code' => $this->qr_code,
			'status' => $this->status,
			'last_charging_at' => $this->last_charging_at
		];
    }
}
