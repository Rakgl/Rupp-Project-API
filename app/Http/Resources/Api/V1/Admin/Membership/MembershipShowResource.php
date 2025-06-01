<?php

namespace App\Http\Resources\Api\V1\Admin\Membership;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'name' => $this->name,
			'image' => Helper::imageUrl($this->image),
			'required_points' => $this->required_points == 0 ? 0 : $this->required_points,
			'point_earned' => $this->point_earned,
			'amount' => $this->amount,
			'status' => $this->status,
			'created_at' => Helper::formatDate($this->created_at),
		];
    }
}
