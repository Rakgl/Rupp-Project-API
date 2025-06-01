<?php

namespace App\Http\Resources\Api\V1\Admin\Report;

use GPBMetadata\Google\Type\Month;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargingTransactionByYearReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		return [		
			'year' => date('Y', strtotime($this->year)),
			'number_of_transaction' => $this->number_of_transaction,
			'total_cost' => number_format($this->total_cost, 2),
			'total_energy' => number_format($this->total_energy, 2),
		];
    }
}

