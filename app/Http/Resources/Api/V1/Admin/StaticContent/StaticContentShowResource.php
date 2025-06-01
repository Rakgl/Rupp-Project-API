<?php

namespace App\Http\Resources\Api\V1\Admin\StaticContent;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaticContentShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$title = json_decode($this->title, true);
		$content = json_decode($this->content, true);
        return [
            'title' => $title['en'],
            'content' => $content['en'],
            'type' => $this->type, 
            'status' => $this->status, 
            'image' => Helper::imageUrl($this->image),
        ];
    }
}
