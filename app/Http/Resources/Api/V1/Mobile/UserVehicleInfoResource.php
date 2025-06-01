<?php

namespace App\Http\Resources\Api\V1\Mobile;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserVehicleInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$brand = $this->brand;
		$model = $this->model;
		$color = $this->color;
		$year = $this->year;
		
		$colorName = $color ? $color->name : null;
		$image = $this->year && $this->year->image ? Helper::imageUrl($this->year->image) : null;
        return [
			'id' => $this->id,
			'brand' => $brand ? $brand->name : 'Default',
			'model' => $model ? $model->name : 'Default',
			'color' => $colorName,
			'image' => $image,
			'year' => $year ? $year->name : 'Default',
			'plate' => $this->plate ? $this->plate : 'Default',
			'is_default' => $this->is_default
		];
    }
}
