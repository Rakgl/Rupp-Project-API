<?php

namespace App\Http\Resources\Api\V1\Admin\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceIndexResource extends JsonResource
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
            'name' => $this->name['en'] ?? null,
            'description' => $this->description['en'] ?? null,
            'price' => $this->price,
            'duration_minutes' => $this->duration_minutes,
            'image_url' => $this->image_url,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}