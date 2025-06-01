<?php

namespace App\Http\Resources\Api\V1\Mobile\Settings\CustomerSupport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSupportIndexResource extends JsonResource
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
            'name' => $this->type,
            'type' => $this->type,
            'contact_info' => $this->contact_info ?? 'N/A',
            'icon' => $this->icon,
            'url' => $this->url,
            'can_call' => in_array($this->type, ['PHONE']),
            'can_message' => in_array($this->type, ['WHATSAPP', 'TELEGRAM']),
            'can_email' => $this->type === 'EMAIL',
            'can_open_url' => !empty($this->url),
        ];
    }
}