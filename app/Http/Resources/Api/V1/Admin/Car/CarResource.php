<?php

namespace App\Http\Resources\Api\V1\Admin\Car;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CarResource extends JsonResource
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
            'model_id' => $this->model_id,
            'body_type_id' => $this->body_type_id,
            'stock_quantity' => $this->stock_quantity,
            'status' => $this->status,
            'year' => $this->year,
            'price' => $this->price,
            'seat' => $this->seat,
            'engine' => $this->engine,
            'door' => $this->door,
            'fuel_type' => $this->fuel_type,
            'condition' => $this->condition,
            'transmission' => $this->transmission,
            'lease_price_per_month' => $this->lease_price_per_month,
            'model' => $this->whenLoaded('model', function () {
                return [
                    'id' => $this->model->id,
                    'name' => $this->model->name,
                    'brand' => $this->when(
                        $this->model->relationLoaded('brand'),
                        fn () => [
                            'id' => $this->model->brand->id,
                            'name' => $this->model->brand->name,
                            'image_url' => $this->model->brand->image_url
                                ? Storage::url($this->model->brand->image_url)
                                : null,
                        ]
                    ),
                ];
            }),
            'body_type' => $this->whenLoaded('bodyType', function () {
                return [
                    'id' => $this->bodyType->id,
                    'name' => $this->bodyType->name,
                    'image_url' => $this->bodyType->image_url
                        ? Storage::url($this->bodyType->image_url)
                        : null,
                ];
            }),
            'primary_image_url' => $this->whenLoaded('images', function () {
                $primary = $this->images->firstWhere('is_primary', true);
                return $primary ? Storage::url($primary->image_path) : null;
            }),
            'images_count' => $this->whenCounted('images'),
            'images' => CarImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
