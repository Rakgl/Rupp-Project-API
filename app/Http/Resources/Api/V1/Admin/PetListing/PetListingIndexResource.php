<?php

namespace App\Http\Resources\Api\V1\Admin\PetListing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetListingIndexResource extends JsonResource
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
            'status' => $this->status,
            'pet' => [
                'id' => $this->pet->id,
                'name' => $this->pet->name,
                'species' => $this->pet->species,
                'breed' => $this->pet->breed,
                'image_url' => $this->pet->image_url,
            ],
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
