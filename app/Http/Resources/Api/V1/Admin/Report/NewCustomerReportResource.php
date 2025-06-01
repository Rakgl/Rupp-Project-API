<?php

namespace App\Http\Resources\Api\V1\Admin\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewCustomerReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		return [
			'phone' => $this->phone,
			'email' => $this->email,
			'created_at' => date('d/m/Y H:i:s', strtotime($this->created_at)),
			'points' => $this->points,
			'balance' => number_format($this->balance, 0),
		];
    }
}

