<?php

namespace App\Http\Resources\Api\V1\Admin\Pet;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetIndexResource extends JsonResource
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
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'species' => $this->species,
            'breed' => $this->breed,
            'weight' => $this->weight,
            'date_of_birth' => $this->date_of_birth ? $this->date_of_birth->format('Y-m-d') : null,
            'image_url' => $this->image_url,
            'medical_notes' => $this->medical_notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}