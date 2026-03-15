<?php

namespace App\Http\Resources\Api\V1\Admin\Favorite;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Admin\Product\ProductIndexResource;
use App\Http\Resources\Api\V1\Admin\Pet\PetIndexResource;
use App\Http\Resources\Api\V1\Admin\Service\ServiceIndexResource;
use App\Http\Resources\Api\V1\Admin\PetListing\PetListingIndexResource;
use App\Models\Product;
use App\Models\Pet;
use App\Models\Service;
use App\Models\PetListing;

class FavoriteIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $favorable = $this->whenLoaded('favorable');
        $favorableData = $favorable;

        if ($favorable instanceof Product) {
            $favorableData = new ProductIndexResource($favorable);
        } elseif ($favorable instanceof Pet) {
            $favorableData = new PetIndexResource($favorable);
        } elseif ($favorable instanceof Service) {
            $favorableData = new ServiceIndexResource($favorable);
        } elseif ($favorable instanceof PetListing) {
            $favorableData = new PetListingIndexResource($favorable);
        }

        return [
            'id'         => $this->id,
            'user'       => $this->whenLoaded('user', function () {
                return [
                    'id'    => $this->user->id,
                    'name'  => $this->user->name,
                    'phone' => $this->user->phone,
                    'email' => $this->user->email,
                    'image' => $this->user->image,
                ];
            }),
            'type'           => strtolower(class_basename($this->favorable_type)),
            'details'        => $favorableData,
            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
