<?php

namespace App\Http\Resources\Api\V1\Mobile\ChargingHistories;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargingHistoryResource extends JsonResource
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
			'station' => $this->station,
			'date' => date('d/m/Y', strtotime($this->created_at)),
			'transaction_number' => $this->transaction_number,
			'cost' => $this->final_cost,
			'charge_point' => $this->charge_point,
			'charger_type' => $this->charger_type,
			'energy' => number_format($this->energy_consumed / 1000, 2),
			'duration' => Helper::formatDuration($this->duration_second),
			'status' => $this->status,
			'start_time' => date('h:i A', strtotime($this->transaction_start_at)),
			'end_time' => date('h:i A', strtotime($this->transaction_stop_at)),
		];
    }
}
