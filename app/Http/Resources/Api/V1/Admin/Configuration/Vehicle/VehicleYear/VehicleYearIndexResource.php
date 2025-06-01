<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Vehicle\VehicleYear;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleYearIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $colorName = null;
    
        if ($this->vehicleColor && $this->vehicleColor->name) {
            $colorName = $this->vehicleColor->name;
        }
    
        return [
            "name" => $this->name,
            "id" => $this->id,
            'color' => $colorName,
            'color_id' => $this->vehicleColor ? $this->vehicleColor->id : null,
            "status" => $this->status,
        ];
    }
    
}
