<?php

namespace App\Http\Resources\Api\V1\Mobile\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Mobile\Product\ProductListResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $itemable = $this->itemable;
        $title = '';
        $categoryName = '';
        $price = 0;
        $imageUrl = null;

        if ($itemable) {
            $title = is_array($itemable->name) ? ($itemable->name['en'] ?? '') : ($itemable->name ?? '');
            $price = (float) $itemable->price;
            $imageUrl = $itemable->image_url;

            // Extract category name if available
            if (method_exists($itemable, 'category') && $itemable->category) {
                $categoryName = is_array($itemable->category->name) ? ($itemable->category->name['en'] ?? '') : $itemable->category->name;
            } else if ($this->itemable_type === 'App\Models\Service') {
                $categoryName = 'Service';
            }
        }

        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'quantity' => $this->quantity,
            'item_type' => class_basename($this->itemable_type),
            'item' => [
                'id' => $itemable ? $itemable->id : null,
                'category_name' => $categoryName, 
                'name' => $title,
                'price' => $price,
                'image_url' => $imageUrl,
            ],
            'subtotal' => round($this->quantity * $price, 2),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
