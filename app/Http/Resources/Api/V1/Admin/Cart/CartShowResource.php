<?php

namespace App\Http\Resources\Api\V1\Admin\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartShowResource extends JsonResource
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
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'           => $this->user->id,
                    'name'         => $this->user->name ?? trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? '')),
                    'phone_number' => $this->user->phone_number ?? null,
                    'email'        => $this->user->email ?? null,
                ];
            }),
            'session_id' => $this->session_id,
            'status' => $this->status,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'subtotal' => round($subtotal, 2),
            'currency' => '$',
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
