<?php

namespace App\Http\Resources\Api\V1\Admin\Reward;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RewardEditResource extends JsonResource
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
			'title' => $this->title,
			'description' => $this->description,
			'points_required' => $this->points_required,
			'reward_value' => $this->reward_value,
			'start_date' => $this->start_date,
			'end_date' => $this->end_date,
			'start_date' => $this->start_date,
            'end_date' => $this->end_date,
			'status' => $this->status,
		];
    }
}
