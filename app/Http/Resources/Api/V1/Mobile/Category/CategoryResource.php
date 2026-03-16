<?php

namespace App\Http\Resources\Api\V1\Mobile\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();
        
        return [
            'id' => $this->id,
            'name' => is_array($this->name) ? ($this->name[$locale] ?? $this->name['en'] ?? null) : $this->name,
            'description' => is_array($this->description) ? ($this->description[$locale] ?? $this->description['en'] ?? null) : $this->description,
            'slug' => $this->slug,
            'type' => $this->type,
            'image_url' => $this->image_url,
            'status' => $this->status,
        ];
    }
}
