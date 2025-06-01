<?php

namespace App\Http\Resources\Api\V1\Mobile\Stations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargingConnectorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$ocppStatus  = $this->ocpp_status == 'Available' ? 'Available' : 'Unavailable';
        return [
			'id' => $this->id,
			'name' => $this->name,
			'qr_code' => $this->qr_code,
			'status' => $ocppStatus
		];
    }
}
