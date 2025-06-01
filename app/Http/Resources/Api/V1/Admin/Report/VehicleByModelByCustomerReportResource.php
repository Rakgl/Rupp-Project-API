<?php

namespace App\Http\Resources\Api\V1\Admin\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleByModelByCustomerReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

		return [
			'brand' => $this->brand,
			'model' => $this->model,
			'number_of_customers' => $this->number_of_customers,
		];
    }
}

