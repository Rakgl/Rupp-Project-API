<?php

namespace App\Http\Resources\Api\V1\Admin\StaticContent;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaticContentIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$locale = $request->locale ? $request->locale : 'en';
		$title = json_decode($this->title, true);
		$content = json_decode($this->content, true);
        return [
            'id' => $this->id,
            'title' => $title ? $title['en'] : $this->title,
            'content' => strip_tags($content[$locale]),
            'type' => $this->type, 
            'status' => Helper::formatStatus($this->status),
            'image' => Helper::imageUrl($this->image),
        ];
    }
}
