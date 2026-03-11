<?php

namespace App\Http\Resources\Api\V1\Admin\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Admin\Product\ProductIndexResource;

class CartItemResource extends JsonResource
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
            'cart_id' => $this->cart_id,
            'item_type' => class_basename($this->itemable_type),
            'item' => $this->whenLoaded('itemable'),
            'quantity' => $this->quantity,
            'subtotal' => round($this->quantity * ($this->itemable->price ?? 0), 2),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
