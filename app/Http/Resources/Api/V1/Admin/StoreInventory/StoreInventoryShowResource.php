<?php

namespace App\Http\Resources\Api\V1\Admin\StoreInventory;

use App\Http\Resources\Api\V1\Admin\Product\ProductShowResource;
use App\Http\Resources\Api\V1\Admin\Store\StoreShowResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreInventoryShowResource extends JsonResource
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
            'store' => new StoreShowResource($this->whenLoaded('store')),
            'product' => new ProductShowResource($this->whenLoaded('product')),
            'stock_quantity' => $this->stock_quantity,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
