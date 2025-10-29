<?php

namespace App\Http\Resources\Api\V1\Admin\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StoreIndexResource extends JsonResource
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
            'logo_url' => $this->logo_url ? Storage::url($this->logo_url) : null, // Assumes logo is in Laravel Storage
            'full_address' => $this->buildFullAddress(),
            'phone_number' => $this->phone_number,
            'telegram' => $this->telegram,
            'operating_hours' => $this->getOperatingHours(),
            'delivers_product' => $this->delivers_product,
            'average_rating' => $this->average_rating,
            'review_count' => $this->review_count,
            'is_verified' => $this->is_verified,
            'is_highlighted' => $this->is_highlighted,
            'is_top_choice' => $this->is_top_choice,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Helper function to build a full, readable address string.
     *
     * @return string
     */
    private function buildFullAddress(): string
    {
        $parts = [
            $this->address,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country,
        ];

        // Filter out empty parts and join with a comma
        return implode(', ', array_filter($parts));
    }

    /**
     * Helper function to create a user-friendly operating hours string.
     *
     * @return string
     */
    private function getOperatingHours(): string
    {
        if ($this->is_24_hours) {
            return 'Open 24 Hours';
        }

        if ($this->opening_time && $this->closing_time) {
            // Assumes times are stored as 'HH:MM:SS'. Converts to 'g:i A' (e.g., "9:00 AM")
            $opening = \Carbon\Carbon::parse($this->opening_time)->format('g:i A');
            $closing = \Carbon\Carbon::parse($this->closing_time)->format('g:i A');
            return "{$opening} - {$closing}";
        }

        return 'N/A'; // Not available
    }
}