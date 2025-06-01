<?php

namespace App\Http\Resources\Api\V1\Admin\Security\User;

use App\Helpers\Helper;
use Illuminate\Http\Resources\Json\JsonResource;

class UserIndexResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'image' => Helper::imageUrl($this->image), 
            'username' => $this->username,
			'role' => $this->role?->name,
			'status' => $this->status,
			'type' => $this->type,
			'created_by' => $this->createdBy ? $this->createdBy->name : '',
			'created_at' => $this->created_at,
			'updated_by' => $this->updatedBy ? 	$this->updatedBy->name : '',
			'updated_at' => $this->updated_at,
			'update_num' => $this->update_num,
			'avatar_fallback_color' => $this->avatar_fallback_color,
			'language' => $this->language,
        ];
    }
}
