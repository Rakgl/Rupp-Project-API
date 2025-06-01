<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\ChargePoint;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargePointIndexResource extends JsonResource
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
            "station" => $this->station,
			"vendor" => $this->vendor ?? 'N/A',
			"model" => $this->model ?? 'N/A',
			"serial_number" => $this->serial_number,
            'factory_serial_number'  => $this->factory_serial_number ?? 'N/A',
			"firmware_version" => $this->firmware_version ?? 'N/A',
			"rate_khr_per_kwh" => $this->rate_khr_per_kwh,
			"min_start_balance" => $this->min_start_balance,
			"max_charging_power" => $this->max_charging_power,
			"type" => $this->type ?? 'N/A',
			'status' => Helper::formatStatus($this->status),
			'ocpp_status' => Helper::formatStatus($this->ocpp_status),
			'has_soc' => $this->has_soc ? 'yes' : 'no',
			'number_of_connectors' => $this->number_of_connectors,
			'can_connect' => $this->can_connect,
		];
    }
}
