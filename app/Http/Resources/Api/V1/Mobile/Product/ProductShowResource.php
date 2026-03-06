<?php

namespace App\Http\Resources\Api\V1\Mobile\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductShowResource extends JsonResource
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
            'name' => is_array($this->name) ? ($this->name[app()->getLocale()] ?? $this->name['en'] ?? null) : $this->name,
            'description' => is_array($this->description) ? ($this->description[app()->getLocale()] ?? $this->description['en'] ?? null) : $this->description,
            'price' => (float) $this->price,
            'image_url' => $this->image_url,
            'attributes' => $this->attributes,
            'category_name' => $this->whenLoaded('category', function() {
                return is_array($this->category->name) ? ($this->category->name[app()->getLocale()] ?? $this->category->name['en'] ?? null) : $this->category->name;
            }),
        ];
    }
}
