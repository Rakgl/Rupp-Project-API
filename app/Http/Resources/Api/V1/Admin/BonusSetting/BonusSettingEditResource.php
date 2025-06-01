<?php

namespace App\Http\Resources\Api\V1\Admin\BonusSetting;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BonusSettingEditResource extends JsonResource
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
			'image' => Helper::imageUrl($this->image),
			'name' => $this->name,
			'description' => $this->description,
			'type' => $this->type,
			'amount' => $this->amount,
			'start_date' => $this->start_date,
            'end_date' => $this->end_date,
			'status' => $this->status,
		];
    }
}
