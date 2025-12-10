<?php

namespace App\Http\Resources\Api\V1\Admin\Review;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'model' => $this->whenLoaded('model', function () {
                return [
                    'id' => $this->model->id,
                    'name' => $this->model->name,
                    'brand' => $this->model->relationLoaded('brand') ? [
                        'id' => $this->model->brand->id,
                        'name' => $this->model->brand->name,
                        'image_url' => $this->model->brand->image_url,
                    ] : null,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
