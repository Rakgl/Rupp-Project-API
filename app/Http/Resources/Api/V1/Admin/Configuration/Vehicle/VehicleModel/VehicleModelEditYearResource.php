<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Vehicle\VehicleModel;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleModelEditYearResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		return[
			'year' => $this->name,
			'image' => Helper::imageUrl($this->image),
			'image_src' => Helper::imageUrl($this->image),
		];
    }
}
