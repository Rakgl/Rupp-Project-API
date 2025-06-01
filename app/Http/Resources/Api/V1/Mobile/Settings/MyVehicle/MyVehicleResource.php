<?php

namespace App\Http\Resources\Api\V1\Mobile\Settings\MyVehicle;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyVehicleResource extends JsonResource
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
		$colorName = $color?->name;
		$year = $this->year;
        return [
			'id' => $this->id,
			'brand' => [
				'id' => $brand ? $brand->id : null,
				'name' => $brand ? $brand->name : null
			],
			'model' => [
				'id' => $model ? $model->id : null,
				'name' => $model ? $model->name : null
			],
			'color' => [
				'id' => $color ? $color->id : null,
				'name' => $colorName
			],
			'year' => [
				'id' => $year ? $year->id : null,
				'name' => $year ? $year->name : null
			],
			'plate' => $this->plate,
			'is_default' => $this->is_default,
			'is_removable' => $this->is_removable,
		];
    }
}
