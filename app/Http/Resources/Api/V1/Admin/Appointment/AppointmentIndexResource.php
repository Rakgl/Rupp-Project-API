<?php

namespace App\Http\Resources\Api\V1\Admin\Appointment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\V1\Admin\Pet\PetIndexResource;
use App\Http\Resources\Api\V1\Admin\Service\ServiceIndexResource;

class AppointmentIndexResource extends JsonResource
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
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'phone' => $this->user->phone,
                    'email' => $this->user->email,
                ];
            }),
            'pet' => new PetIndexResource($this->whenLoaded('pet')),
            'service' => new ServiceIndexResource($this->whenLoaded('service')),
            'status' => $this->status,
            'start_time' => $this->start_time?->format('Y-m-d H:i:s'),
            'end_time' => $this->end_time?->format('Y-m-d H:i:s'),
            'special_requests' => $this->special_requests,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
