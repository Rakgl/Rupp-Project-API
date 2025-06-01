<?php

namespace App\Http\Resources\Api\V1\Admin\Overview;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OverviewStationResource extends JsonResource
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
			'lat' => $this->latitude,
			'lng' => $this->longitude,
			'name' => $this->name,
			'hour' => $this->open_hours . '-' . $this->close_hours,
			'charger' => $this->unavailable_connectors . '/' . $this->total_connectors,
			'status' => $this->status
		];
    }
}
