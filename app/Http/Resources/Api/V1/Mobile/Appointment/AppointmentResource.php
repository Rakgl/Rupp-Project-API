<?php

namespace App\Http\Resources\Api\V1\Mobile\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Mobile\Pet\PetResource;
use App\Http\Resources\Api\V1\Mobile\Service\ServiceResource;

class AppointmentResource extends JsonResource
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
            'status' => $this->status,
            'start_time' => $this->start_time?->format('Y-m-d H:i:s'),
            'end_time' => $this->end_time?->format('Y-m-d H:i:s'),
            'special_requests' => $this->special_requests,
            'pet' => new PetResource($this->whenLoaded('pet')),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
