<?php

namespace App\Http\Resources\Api\V1\Mobile\PetListing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\V1\Mobile\Pet\PetResource;

class PetListingResource extends JsonResource
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
            'listing_type' => $this->listing_type,
            'price' => (float) $this->price,
            'description' => $this->description,
            'status' => $this->status,
            'pet' => new PetResource($this->whenLoaded('pet')),
            'is_favorite' => Auth::check() ? $this->favorites()->where('user_id', Auth::id())->exists() : false,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
