<?php

namespace App\Http\Resources\Api\V1\Admin\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderIndexResource extends JsonResource
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
            'user' => [ // Fallback inline user info or dedicated resource if available
                'id' => $this->whenLoaded('user') ? $this->user->id : null,
                'name' => $this->whenLoaded('user') ? $this->user->name : null,
                'email' => $this->whenLoaded('user') ? $this->user->email : null,
            ],
            'store' => new \App\Http\Resources\Api\V1\Admin\Store\StoreIndexResource($this->whenLoaded('store')),
            'total_amount' => $this->total_amount,
            'fulfillment_type' => $this->fulfillment_type,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
