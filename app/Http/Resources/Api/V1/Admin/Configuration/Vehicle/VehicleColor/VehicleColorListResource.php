<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Vehicle\VehicleColor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleColorListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = $request->locale ? $request->locale : 'en';
		$name = json_decode($this->name, true);
        return [
			"id" => $this->id, 
			"color_code" => $this->color_code, 
			"name" => isset($name[$locale]) ? $name[$locale] : null,
            'model' => $this->vehicleModel ? $this->vehicleModel->name : null,
            'model_id' => $this->vehicleModel ? $this->vehicleModel->id : null,      
			'status' => $this->status
		];
    }
}
