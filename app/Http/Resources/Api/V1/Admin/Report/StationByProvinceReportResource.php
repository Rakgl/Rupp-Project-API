<?php

namespace App\Http\Resources\Api\V1\Admin\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationByProvinceReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		return [
			'province' => $this->province,
			'number_of_stations' => number_format($this->number_of_stations, 0),
			// 'number_of_charge_points' => number_format($this->number_of_charge_points, 0),
			// 'number_of_chargers' => number_format($this->number_of_chargers, 0),
		];
    }
}

