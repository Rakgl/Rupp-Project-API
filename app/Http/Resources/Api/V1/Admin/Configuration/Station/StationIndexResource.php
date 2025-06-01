<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Station;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$stationImage = $this->images()->count() > 0 ? $this->images()->first() : null;
        return [
            'id' => $this->id,
			'province' => $this->province ? $this->province : null,
            'name' => $this->name,
            'image' => Helper::imageUrl($this->cover),
            'open_hours' => $this->open_hours,
            'close_hours' => $this->close_hours,
            'address' => $this->address,
            'phone' => $this->phone,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'amenity' => $this->amenity,
			'status' => Helper::formatStatus($this->status),
			'ocpp_status' => $this->ocpp_status,
			'allow_charging' => $this->allow_charging
        ];
    }
}
