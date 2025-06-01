<?php

namespace App\Http\Resources\Api\V1\Admin\Configuration\Station;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $stationImages = $this->images()->get();
        $images = [];

        // Add cover image first if it exists
        if ($this->cover) {
            $images[] = Helper::imageUrl($this->cover);
        }

        // Add additional station images
        foreach ($stationImages as $stationImage) {
            $images[] = Helper::imageUrl($stationImage->image);
        }

        $amenity = strip_tags($this->amenity);

        return [
            'province'    => $this->province->name,
            'name'        => $this->name,
            'images'      => $images,
            'open_hours'  => $this->open_hours,
            'close_hours' => $this->close_hours,
            'address'     => $this->address,
            'phone'       => $this->phone,
            'longitude'   => $this->longitude,
            'latitude'    => $this->latitude,
            'amenity'     => $amenity,
            'ocpp_status' => $this->ocpp_status,
            'cover'       => Helper::imageUrl($this->cover),
            'status'      => $this->status,
            'created_by'  => $this->createdBy ? $this->createdBy->name : '',
            'created_at'  => date('d/m/Y , h:i A', strtotime($this->created_at)),
        ];
    }
}
