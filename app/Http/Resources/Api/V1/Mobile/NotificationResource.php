<?php

namespace App\Http\Resources\Api\V1\Mobile;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
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
		$message = json_decode($this->message, true);
        return [
			'id' => $this->id,
			'title' => $title ? $title[$locale] : $this->title,
			'message' => $message ? $message[$locale] : $this->message,
			'type' => $this->type,
			'date' => date('d M Y', strtotime($this->created_at)),
			'time' => date('h:i A', strtotime($this->created_at)),
			'image' => Helper::imageUrl($this->image),
			'is_read' => $this->is_read,
		];
    }
}
