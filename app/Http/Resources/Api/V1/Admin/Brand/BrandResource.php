<?php

namespace App\Http\Resources\Api\V1\Admin\Brand;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BrandResource extends JsonResource
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
            'name' => $this->name,
            'image_url' => $this->image_url ? Storage::url($this->image_url) : null,
            'models_count' => $this->whenCounted('models'),
            'models' => $this->whenLoaded('models'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
