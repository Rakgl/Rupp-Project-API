<?php

namespace App\Http\Resources\Api\V1\Mobile\Stations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationMapResource extends JsonResource
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
			'distance' => number_format($this->distance, 2, '.', '' ) . ' km',
			'lat' => $this->latitude,
			'lng' => $this->longitude,
		];
    }
}
