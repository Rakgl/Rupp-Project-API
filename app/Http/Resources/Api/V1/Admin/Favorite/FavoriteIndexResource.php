<?php

namespace App\Http\Resources\Api\V1\Admin\Favorite;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Admin\Product\ProductIndexResource;

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
        return [
            'id'         => $this->id,
            'user'       => $this->whenLoaded('user', function () {
                return [
                    'id'           => $this->user->id,
                    'first_name'   => $this->user->first_name ?? null,
                    'last_name'    => $this->user->last_name ?? null,
                    'name'         => $this->user->name ?? trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? '')),
                    'phone_number' => $this->user->phone_number ?? null,
                    'email'        => $this->user->email ?? null,
                ];
            }),
            'favorable_type' => class_basename($this->favorable_type),
            'favorable'  => $this->whenLoaded('favorable'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
