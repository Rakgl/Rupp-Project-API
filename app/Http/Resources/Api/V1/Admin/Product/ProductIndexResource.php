<?php

namespace App\Http\Resources\Api\V1\Admin\Product;

use App\Http\Resources\Api\V1\Admin\Category\CategoryShowResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductIndexResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => is_array($this->description) ? ($this->description['en'] ?? null) : $this->description,
            'attributes' => $this->attributes,
            'price' => (float) $this->price,
            'image_url' => $this->image_url,
            'sku' => $this->sku,
            'status' => $this->status,
            'category' => new CategoryShowResource($this->whenLoaded('category')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
