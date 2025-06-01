<?php

namespace App\Http\Resources\Api\V1\Mobile\Stations;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SavedStationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$allAvailableConnectors = $this->available_dc_connectors + $this->available_ac_connectors;
		$allConnectors = $this->all_dc_connectors + $this->all_ac_connectors;
		$status  = $allAvailableConnectors == $allConnectors ? 'Unavailable' : 'Available';

		$isShowDC = true;
		if($this->available_dc_connectors == 0 && $this->all_dc_connectors == 0) {
			$isShowDC = false;
		}

		$isShowAC = true;
		if($this->available_ac_connectors == 0 && $this->all_ac_connectors == 0) {
			$isShowAC = false;
		}
		$stationImage = $this->images()->count() > 0 ? $this->images()->first() : null;
        return [
			'id' => $this->id,
			'name' => $this->name,
			'image' => Helper::imageUrl($stationImage?->image),
			'address' => $this->address,
			'phone' => $this->phone,
			'distance' => number_format($this->distance, 2, '.', '' ) . ' km',
			'lat' => $this->latitude,
			'lng' => $this->longitude,
			'dc_connector' => $this->available_dc_connectors . '/' . $this->all_dc_connectors,
			'ac_connector' => $this->available_ac_connectors . '/' . $this->all_ac_connectors,
			'status' => $status,
			'low_price' => $this->low_price,
			'is_show_dc' => $isShowDC,
			'is_show_ac' => $isShowAC
		];
    }
}
