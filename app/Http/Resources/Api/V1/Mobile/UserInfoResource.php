<?php

namespace App\Http\Resources\Api\V1\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$idTag = $this->idTag ? $this->idTag->tag_value : null;
        return [
			'name' => $this->name,
			'phone' => $this->phone,
			'email' => $this->email,
			'balance' => number_format($this->balance, 0) . ' KHR',
			'id_tag' => $idTag,
			'has_received_bonus' => $this->has_received_bonus
		];
    }
}
