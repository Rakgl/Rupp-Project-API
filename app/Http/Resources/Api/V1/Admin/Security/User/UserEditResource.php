<?php

namespace App\Http\Resources\Api\V1\Admin\Security\User;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserEditResource extends JsonResource
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
			'email' => $this->email ? $this->email : null,
			'username' => $this->username,
			'image' => Helper::imageUrl($this->image),
			'status' => $this->status,
			'role_id' => $this->role_id,
		];
    }
}
