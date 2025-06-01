<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Station;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationPDFResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		return [
			'province' => $this->province->name,
            'name' => $this->name,
            'open_hours' => $this->open_hours,
            'close_hours' => $this->close_hours,
            'address' => $this->address,
            'phone' => $this->phone,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'address' => $this->address,
            'amenity' => $this->amenity,
            'status' => $this->status,
			'created_by' => $this->createdBy ? $this->createdBy->name : '',
			'created_at' => date('d/m/Y , h:i A', strtotime($this->created_at)),
		];
    }
}
