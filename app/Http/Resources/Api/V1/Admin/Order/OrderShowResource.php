<?php

namespace App\Http\Resources\Api\V1\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderShowResource extends JsonResource
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
            'user' => [
                'id' => $this->whenLoaded('user') ? $this->user->id : null,
                'name' => $this->whenLoaded('user') ? $this->user->name : null,
                'email' => $this->whenLoaded('user') ? $this->user->email : null,
                'phone' => $this->whenLoaded('user') ? $this->user->phone : null,
            ],
            'store' => new \App\Http\Resources\Api\V1\Admin\Store\StoreShowResource($this->whenLoaded('store')),
            'payment_method' => $this->whenLoaded('paymentMethod'),
            'total_amount' => $this->total_amount,
            'fulfillment_type' => $this->fulfillment_type,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'delivery_address' => $this->delivery_address,
            'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
