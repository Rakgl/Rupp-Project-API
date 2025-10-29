<?php

namespace App\Http\Resources\Api\V1\Admin\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StoreShowResource extends JsonResource
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
            'logo_url' => $this->logo_url ? Storage::url($this->logo_url) : null,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'full_address' => $this->buildFullAddress(), // Convenience field
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'license_number' => $this->license_number,
            'operating_hours' => $this->getOperatingHours(),
			'opening_time' => $this->opening_time ? \Carbon\Carbon::parse($this->opening_time)->format('H:i') : null,
            'closing_time' => $this->closing_time ? \Carbon\Carbon::parse($this->closing_time)->format('H:i') : null,
            'is_24_hours' => (bool) $this->is_24_hours,
            'delivers_product' => (bool) $this->delivers_product,
            'order_send' => $this->order_send,
            'delivery_details' => $this->delivery_details,
            'is_verified' => (bool) $this->is_verified,
            'is_highlighted' => (bool) $this->is_highlighted,
            'is_top_choice' => (bool) $this->is_top_choice,
            'average_rating' => $this->average_rating,
            'review_count' => $this->review_count,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Helper to build a full, readable address string.
     */
    private function buildFullAddress(): string
    {
        $parts = [$this->address, $this->city, $this->state, $this->zip_code, $this->country];
        return implode(', ', array_filter($parts));
    }

    /**
     * Helper to create a user-friendly operating hours string.
     */
    private function getOperatingHours(): string
    {
        if ($this->is_24_hours) {
            return 'Open 24 Hours';
        }
        if ($this->opening_time && $this->closing_time) {
            $opening = \Carbon\Carbon::parse($this->opening_time)->format('g:i A');
            $closing = \Carbon\Carbon::parse($this->closing_time)->format('g:i A');
            return "{$opening} - {$closing}";
        }
        return 'N/A';
    }
}