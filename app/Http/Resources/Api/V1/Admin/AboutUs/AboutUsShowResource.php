<?php

namespace App\Http\Resources\Api\V1\Admin\AboutUs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AboutUsShowResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'list_text' => $this->list_text,
            'image_url' => $this->image_url ? Storage::url($this->image_url) : null,
            'status' => $this->status,
            // 'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            // 'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}