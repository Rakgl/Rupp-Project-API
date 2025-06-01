<?php

namespace App\Http\Resources\Api\V1\Mobile;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaticContentResource extends JsonResource
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
		$locale = $request->locale ?? 'en';
        return [
			'id' => $this->id,
			'title' => $title ? $title[$locale] : $this->title,
			'content' => $content ? $content[$locale] : $this->content,
			'type' => $this->type,
			'image' => Helper::imageUrl($this->image),
		];
    }
}
