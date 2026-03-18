<?php

namespace App\Http\Resources\Api\V1\Mobile\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Mobile\Product\ProductListResource;

class OrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'subtotal' => (float) $this->subtotal,
            'delivery_fee' => (float) $this->delivery_fee,
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'fulfillment_type' => $this->fulfillment_type,
            'delivery_address' => $this->delivery_address,
            'items' => $this->whenLoaded('orderItems', function() {
                return $this->orderItems->map(function($item) {
                    $itemable = $item->itemable;
                    $name = null;
                    $imageUrl = null;

                    if ($itemable) {
                        if ($item->itemable_type === \App\Models\Product::class || $item->itemable_type === \App\Models\Service::class) {
                            $name = is_array($itemable->name) ? ($itemable->name[app()->getLocale()] ?? $itemable->name['en'] ?? null) : $itemable->name;
                            $imageUrl = $itemable->image_url;
                        } elseif ($item->itemable_type === \App\Models\Pet::class) {
                            $name = $itemable->name;
                            $imageUrl = $itemable->image_url;
                        } elseif ($item->itemable_type === \App\Models\PetListing::class) {
                            $name = $itemable->pet?->name;
                            $imageUrl = $itemable->pet?->image_url;
                        }
                    }

                    return [
                        'id' => $item->id,
                        'itemable_id' => $item->itemable_id,
                        'itemable_type' => $item->itemable_type,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'subtotal' => (float) $item->subtotal,
                        'item_name' => $name,
                        'image_url' => $imageUrl,
                    ];
                });
            }),
            'payment_method' => $this->whenLoaded('paymentMethod', function() {
                return [
                    'id' => $this->paymentMethod->id,
                    'name' => $this->paymentMethod->name,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
