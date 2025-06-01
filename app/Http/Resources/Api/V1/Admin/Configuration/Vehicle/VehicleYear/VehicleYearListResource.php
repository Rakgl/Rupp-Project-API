<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Vehicle\VehicleYear;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleYearListResource extends JsonResource
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
			"name" => isset($name[$locale]) ? $name[$locale] : null,
		];

    }
}
