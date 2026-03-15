<?php

namespace App\Http\Resources\Api\V1\Mobile\Pet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PetResource extends JsonResource
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
            'species' => $this->species,
            'breed' => $this->breed,
            'weight' => (float) $this->weight,
            'date_of_birth' => $this->date_of_birth ? $this->date_of_birth->format('Y-m-d') : null,
            'price' => $this->price,
            'image_url' => $this->image_url,
            'medical_notes' => $this->medical_notes,
            'category_name' => $this->whenLoaded('category', function() {
                return is_array($this->category->name) ? ($this->category->name[app()->getLocale()] ?? $this->category->name['en'] ?? null) : $this->category->name;
            }),
            'is_favorite' => Auth::check() ? $this->favorites()->where('user_id', Auth::id())->exists() : false,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
