<?php

namespace App\Http\Resources\Api\V1\Admin\Customer;

use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerIndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $idTag = $this->id_tag;

        return [
            'id' => $this->id,
            'name' => $this->name ?? 'N/A',
            'country_code' => $this->country_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => Helper::formatStatus($this->status),
            'created_at' => Helper::formatDate($this->created_at),
            'balance' => number_format($this->balance, 2) . ' KHR',
            'points' => number_format($this->points, 2) . ' Points',
            'membership' => $this->membership?->name,
            'free_forever' => $idTag?->free_forever ?? false,
            'free_from' => $idTag?->free_from,
            'free_to' => $idTag?->free_to,
            'id_tag' => $idTag?->tag_value,
        ];
    }
}
