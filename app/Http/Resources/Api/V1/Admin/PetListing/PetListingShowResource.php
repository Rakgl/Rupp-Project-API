<?php

namespace App\Http\Resources\Api\V1\Admin\PetListing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetListingShowResource extends JsonResource
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
            'pet_id' => $this->pet_id,
            'listing_type' => $this->listing_type,
            'price' => $this->price,
            'description' => $this->description,
            'status' => $this->status,
            'pet' => [
                'id' => $this->pet->id,
                'name' => $this->pet->name,
                'species' => $this->pet->species,
                'breed' => $this->pet->breed,
                'weight' => $this->pet->weight,
                'date_of_birth' => $this->pet->date_of_birth ? $this->pet->date_of_birth->toDateString() : null,
                'image_url' => $this->pet->image_url,
                'medical_notes' => $this->pet->medical_notes,
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
