<?php

namespace App\Http\Resources\Api\V1\Mobile\Service;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ServiceResource extends JsonResource
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
            'duration_minutes' => $this->duration_minutes,
            'image_url' => $this->image_url,
            'is_favorite' => Auth::check() ? $this->favorites()->where('user_id', Auth::id())->exists() : false,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
