<?php

namespace App\Http\Resources\Api\V1\Admin\Membership;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipEditResource extends JsonResource
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
			'image' => Helper::imageUrl($this->image),
			'required_points' => $this->required_points == 0 ? 0 : $this->required_points,
			'point_earned' => $this->point_earned,
			'status' => $this->status,
			'amount' => $this->amount,
			'created_at' => date('d/m/Y , h:i A', strtotime($this->created_at)),
		];
    }
}
