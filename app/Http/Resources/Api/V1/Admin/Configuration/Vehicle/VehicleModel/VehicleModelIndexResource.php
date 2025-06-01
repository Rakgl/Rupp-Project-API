<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Vehicle\VehicleModel;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleModelIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$colors = $this->vehicleColors->pluck('name');
	
		return [
			'id' => $this->id,
			'name' => $this->name,
      		'image' => Helper::imageUrl($this->image),
			'brand' => $this->vehicleBrand ? $this->vehicleBrand->name : null,
			'status' => Helper::formatStatus($this->status),
			'colors' => $colors	
		];
    }
}
