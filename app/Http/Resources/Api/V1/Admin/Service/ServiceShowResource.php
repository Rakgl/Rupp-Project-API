<?php

namespace App\Http\Resources\Api\V1\Admin\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceShowResource extends JsonResource
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
            'name' => is_array($this->name) ? ($this->name['en'] ?? null) : $this->name,
            'description' => is_array($this->description) ? ($this->description['en'] ?? null) : $this->description,
            'price' => $this->price,
            'duration_minutes' => $this->duration_minutes,
            'image_url' => $this->image_url,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}