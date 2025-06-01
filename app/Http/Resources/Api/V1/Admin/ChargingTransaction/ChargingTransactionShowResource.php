<?php

namespace App\Http\Resources\Api\V1\Admin\ChargingTransaction;

use App\Helpers\Helper;
use App\Models\IdTag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Maatwebsite\Excel\Concerns\ToArray;

class ChargingTransactionShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {	
		return [
			'date' => Helper::formatDate($this->created_at),
			'cost' => $this->final_cost . ' KHR',
			'transaction_number' => $this->transaction_number,
			'station' => $this->station,
			'charge_point' => $this->charge_point,
			'duration' => Helper::formatDuration($this->duration_second),
			'energy' => number_format($this->energy_consumed, 2) . ' kWh',
			'start_time' => date('h:i A', strtotime($this->transaction_start_at)),
			'end_time' => date('h:i A', strtotime($this->transaction_stop_at)),
		];
    }
}
