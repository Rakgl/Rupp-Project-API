<?php

namespace App\Http\Resources\Api\V1\Admin\Security\User;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'type' => $this->type,
			'ip_address' => $this->ip_address,
			'browser' => $this->browser,
			'login_at'=> $this->login_at ? date('d/m/Y h:i:s, A', strtotime($this->login_at)) : '',
			'logout_at'=> $this->logout_at ? date('d/m/Y h:i:s, A', strtotime($this->logout_at)) : '',
		];
    }
}
