<?php

namespace App\Http\Resources\Api\V1\Mobile\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Auth;

use App\Models\Store;

class ProductShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultStore = Store::first();
        $inventory = $this->storeInventories->where('store_id', $defaultStore?->id)->first();
        $stockQuantity = $inventory ? $inventory->stock_quantity : 0;
        
        $stockStatus = 'IN_STOCK';
        if ($stockQuantity <= 0) {
            $stockStatus = 'OUT_OF_STOCK';
        } elseif ($stockQuantity < 20) {
            $stockStatus = 'LOW_STOCK';
        }

        return [
            'id' => $this->id,
            'name' => is_array($this->name) ? ($this->name[app()->getLocale()] ?? $this->name['en'] ?? null) : $this->name,
            'price' => (float) $this->price,
            'image_url' => $this->image_url,
            'description' => is_array($this->description) ? ($this->description[app()->getLocale()] ?? $this->description['en'] ?? null) : $this->description,
            'category_name' => $this->whenLoaded('category', function() {
                return is_array($this->category->name) ? ($this->category->name[app()->getLocale()] ?? $this->category->name['en'] ?? null) : $this->category->name;
            }),
            'is_favorite' => Auth::check() ? $this->favorites()->where('user_id', Auth::id())->exists() : false,
            'sku' => $this->sku,
            'status' => $this->status,
            'stock_quantity' => $stockQuantity,
            'stock_status' => $stockStatus,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
