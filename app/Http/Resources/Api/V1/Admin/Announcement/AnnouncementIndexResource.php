<?php

namespace App\Http\Resources\Api\V1\Admin\Announcement;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $title = is_array($this->title) ? $this->title : json_decode($this->title, true);
        $message = is_array($this->message) ? $this->message : json_decode($this->message, true);
		$message  = strip_tags($message['en']);

        return [
            'id' => $this->id,
            'title' => $title['en'] ?? $title, 
            'message' => $message,
            'type' => $this->type,
            'scheduled_at' => Helper::formatDate($this->scheduled_at),
			'status' => Helper::formatStatus($this->status),
            'image' => Helper::imageUrl($this->image),
            'sent_at' => $this->sent_at,
            'sent_by' => $this->sent_by,
        ];
    }
}
