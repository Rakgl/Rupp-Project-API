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
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'fulfillment_type' => $this->fulfillment_type,
            'delivery_address' => $this->delivery_address,
            'items' => $this->whenLoaded('orderItems', function() {
                return $this->orderItems->map(function($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'subtotal' => (float) $item->subtotal,
                        'product_name' => is_array($item->product->name) ? ($item->product->name[app()->getLocale()] ?? $item->product->name['en'] ?? null) : $item->product->name,
                        'image_url' => $item->product->image_url,
                    ];
                });
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
