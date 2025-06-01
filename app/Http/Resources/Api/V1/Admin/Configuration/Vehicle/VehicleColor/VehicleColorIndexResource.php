<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Vehicle\VehicleColor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleColorIndexResource extends JsonResource
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
            'model' => $this->vehicleModel ? $this->vehicleModel->name : null,
            'model_id' => $this->vehicleModel ? $this->vehicleModel->id : null,      
			'status' => $this->status
		];
    }
}
