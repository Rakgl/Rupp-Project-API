<?php

namespace App\Http\Resources\Api\V1\Admin\Overview;

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
			'lat' => $this->latitude,
			'lng' => $this->longitude,
			'title' => $this->name,
			'current' => $this->unavailable_connectors,
			'total' => $this->total_connectors,
			'status' => $this->status
		];
    }
}
