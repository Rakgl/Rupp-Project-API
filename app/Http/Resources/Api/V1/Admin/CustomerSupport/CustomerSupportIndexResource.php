<?php

namespace App\Http\Resources\Api\V1\Admin\CustomerSupport;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerSupportIndexResource extends JsonResource
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
			'type' => $this->type,
			'contact_info' => $this->contact_info,
			'url' => $this->url,
			'display_order' => $this->display_order,
			'status' => Helper::formatStatus($this->status),
		];
    }
}
