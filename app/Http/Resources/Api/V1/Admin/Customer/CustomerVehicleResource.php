<?php

namespace App\Http\Resources\Api\V1\Admin\Customer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerVehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {	
		$brand = $this->brand;
		$color = $this->color;	
		$model = $this->model;
		$year = $this->year;

		$colorName = json_decode($color?->name, true);

        return [
			'image' => $model?->image,
			'brand' => $brand ? $brand->name : null,
			'model' => $model ? $model->name : null,
			'color' => $colorName ? $colorName['en'] : null,
			'year' => $year ? $year->name : null,
			'plate' => $this->plate,
		];
    }
}
