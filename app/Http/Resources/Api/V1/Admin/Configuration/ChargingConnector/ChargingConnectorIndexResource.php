<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\ChargingConnector;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargingConnectorIndexResource extends JsonResource
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
			'station' => $this->station,
			'charge_point' => $this->chargePoint,
			'serial_number' => $this->serial_number,
			'connector_number' => $this->connector_number,
			'qr_code' => $this->qr_code,
			'status' => Helper::formatStatus($this->status),
			'ocpp_status' => Helper::formatStatus($this->ocpp_status),
			'ocpp_charge_point_status' => $this->ocpp_charge_point_status
		];
    }
}
