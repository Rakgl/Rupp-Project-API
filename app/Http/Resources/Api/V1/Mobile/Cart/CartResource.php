<?php

namespace App\Http\Resources\Api\V1\Mobile\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subtotal = 0;
        if ($this->relationLoaded('items')) {
            $subtotal = $this->items->sum(function ($item) {
                return $item->quantity * ($item->itemable->price ?? 0);
            });
        }

        return [
            'id' => $this->id,
            'status' => $this->status,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'subtotal' => round($subtotal, 2),
            'currency' => '$', // Based on the UI
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
