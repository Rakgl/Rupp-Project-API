<?php

namespace App\Http\Resources\Api\V1\Mobile\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Auth;

class ProductListResource extends JsonResource
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
            'price' => (float) $this->price,
            'image_url' => $this->image_url,
            'category_name' => $this->whenLoaded('category', function() {
                return is_array($this->category->name) ? ($this->category->name[app()->getLocale()] ?? $this->category->name['en'] ?? null) : $this->category->name;
            }),
            'is_favorite' => Auth::check() ? $this->favorites()->where('user_id', Auth::id())->exists() : false,
        ];
    }
}
