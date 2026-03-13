<?php

namespace App\Http\Resources\Api\V1\Mobile\Favorite;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Mobile\Product\ProductListResource;
use App\Http\Resources\Api\V1\Admin\Pet\PetIndexResource;
use App\Http\Resources\Api\V1\Admin\Service\ServiceIndexResource;
use App\Http\Resources\Api\V1\Admin\PetListing\PetListingIndexResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $favorable = $this->favorable;
        $type = null;
        $details = null;

        if ($favorable instanceof \App\Models\Product) {
            $type = 'product';
            $details = new ProductListResource($favorable);
        } elseif ($favorable instanceof \App\Models\Pet) {
            $type = 'pet';
            $details = new PetIndexResource($favorable);
        } elseif ($favorable instanceof \App\Models\Service) {
            $type = 'service';
            $details = new ServiceIndexResource($favorable);
        } elseif ($favorable instanceof \App\Models\PetListing) {
            $type = 'pet_listing';
            $details = new PetListingIndexResource($favorable);
        }

        return [
            'id' => $this->id,
            'type' => $type,
            'details' => $details,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
