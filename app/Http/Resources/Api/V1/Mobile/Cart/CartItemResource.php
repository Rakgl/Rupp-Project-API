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
        $productTitle = is_array($this->product->name) ? ($this->product->name['en'] ?? '') : ($this->product->name ?? '');
        $categoryName = $this->product->category ? (is_array($this->product->category->name) ? ($this->product->category->name['en'] ?? '') : $this->product->category->name) : '';
        
        // Use category name as the subtitle based on the UI screenshot 
        // e.g., "Dogs" then "American Bully"
        
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'quantity' => $this->quantity,
            'product' => [
                'id' => $this->product->id,
                'category_name' => $categoryName, // E.g., Dogs, Toy
                'name' => $productTitle, // E.g., American Bully, Pug, Green
                'price' => (float) $this->product->price,
                'image_url' => $this->product->image_url,
            ],
            'subtotal' => round($this->quantity * ($this->product->price ?? 0), 2),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
