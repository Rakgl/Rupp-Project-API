<?php

namespace App\Http\Resources\Api\V1\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $itemable = $this->whenLoaded('itemable');
        $name = null;
        $imageUrl = null;

        if ($itemable) {
            if ($this->itemable_type === \App\Models\Product::class || $this->itemable_type === \App\Models\Service::class) {
                $name = is_array($itemable->name) ? ($itemable->name[app()->getLocale()] ?? $itemable->name['en'] ?? null) : $itemable->name;
                $imageUrl = $itemable->image_url ?? null;
            } elseif ($this->itemable_type === \App\Models\Pet::class) {
                $name = $itemable->name;
                $imageUrl = $itemable->image_url ?? null;
            } elseif ($this->itemable_type === \App\Models\PetListing::class) {
                $name = $itemable->pet?->name;
                $imageUrl = $itemable->pet?->image_url ?? null;
            }
        }

        return [
            'id' => $this->id,
            'itemable_id' => $this->itemable_id,
            'itemable_type' => $this->itemable_type,
            'item_name' => $name,
            'image_url' => $imageUrl,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'subtotal' => $this->subtotal,
        ];
    }
}
