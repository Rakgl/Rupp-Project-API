<?php

namespace App\Http\Resources\Api\V1\Mobile\CustomerWallets;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
			'amount' => number_format($this->final_cost, 2, '.', ','),
			'energy' => number_format($this->energy_consumed, 2),
			'title' => $this->station,
			'date' => Helper::formatDate($this->created_at),
			'payment_method' => $this->payment_method,
			'rate_khr_per_kwh' => $this->rate_khr_per_kwh,
			'duration' => Helper::formatDuration($this->duration_second),
			'start_time' => Helper::formatDate($this->transaction_start_at),
			'end_time' => Helper::formatDate($this->transaction_stop_at),
			'charge_point' => $this->charge_point
		];
    }
}
