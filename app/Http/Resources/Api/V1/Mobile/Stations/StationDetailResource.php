<?php

namespace App\Http\Resources\Api\V1\Mobile\Stations;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StationDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->all_available_connectors == $this->all_connectors ? 'UNAVAILABLE' : 'AVAILABLE';

        $isShowDC = true;
        if ($this->all_available_dc_connectors == 0 && $this->all_dc_connectors == 0) {
            $isShowDC = false;
        }

        $isShowAC = true;
        if ($this->all_available_ac_connectors == 0 && $this->all_ac_connectors == 0) {
            $isShowAC = false;
        }

        $stationImages = $this->images()->get();
        $images = [];

        // Add cover image first if it exists
        if ($this->cover) {
            $images[] = Helper::imageUrl($this->cover);
        }

        foreach ($stationImages as $stationImage) {
            $images[] = Helper::imageUrl($stationImage->image);
        }

        $chargePoints = $this->chargePoints()->get();

        return [
            'id'           => $this->id,
            'distance'     => number_format($this->distance, 2, '.', '') . ' km',
            'images'       => $images,
            'name'         => $this->name,
            'address'      => $this->address,
            'phone'        => $this->phone,
            'total_port'   => $this->all_available_connectors . '/' . $this->all_connectors,
            'dc_connector' => $this->all_available_dc_connectors . '/' . $this->all_dc_connectors,
            'ac_connector' => $this->all_available_ac_connectors . '/' . $this->all_ac_connectors,
            'lat'          => $this->latitude,
            'lng'          => $this->longitude,
            'type'         => $this->type,
            'status'       => $status,
            'low_price'    => number_format($this->low_price, 0, '.', ','),
            'charge_points'=> ChargePointResource::collection($chargePoints),
            'is_show_dc'   => $isShowDC,
            'is_show_ac'   => $isShowAC,
            'amenity'      => $this->amenity
        ];
    }
}
