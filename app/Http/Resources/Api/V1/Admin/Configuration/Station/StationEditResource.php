<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Station;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$stationImages = $this->images()->get();
		$images = [];
		foreach ($stationImages as $stationImage) {
			$images[] = Helper::imageUrl($stationImage->image);
		}
		return [
			'id' => $this->id,
			'province' => $this->province,
            'name' => $this->name,
			'images' => $images,
            'open_hours' => $this->open_hours,
            'close_hours' => $this->close_hours,
            'address' => $this->address,
            'phone' => $this->phone,
            'type' => $this->type == 'AC' ? "AC":"DC",
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'amenity' => $this->amenity,
            'status' => $this->status,
			'ocpp_status' => $this->ocpp_status,
            'capacity' => $this->capacity,
			'cover' => Helper::imageUrl($this->cover),
			'allow_charging' => $this->allow_charging
		];
    }
}
