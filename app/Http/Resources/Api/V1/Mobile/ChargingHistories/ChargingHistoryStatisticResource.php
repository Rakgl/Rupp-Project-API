<?php

namespace App\Http\Resources\Api\V1\Mobile\ChargingHistories;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargingHistoryStatisticResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'total_cost' => number_format($this->total_cost, 2),
			'total_energy' => number_format($this->total_energy / 1000, 2),
			'total_duration' => Helper::formatDuration($this->total_duration),
			'number_of_transaction' => $this->number_of_transaction
		];
    }
}
