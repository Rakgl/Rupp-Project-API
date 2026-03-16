<?php

namespace App\Http\Resources\Api\V1\Mobile\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'phone_number' => $this->phone_number,
            'telegram' => $this->telegram,
            'email' => $this->email,
            'opening_time' => $this->opening_time,
            'closing_time' => $this->closing_time,
            'is_24_hours' => (bool) $this->is_24_hours,
            'average_rating' => (float) $this->average_rating,
            'review_count' => (int) $this->review_count,
            'is_verified' => (bool) $this->is_verified,
            'status' => $this->status,
        ];
    }
}
