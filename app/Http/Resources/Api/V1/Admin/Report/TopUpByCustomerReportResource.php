<?php

namespace App\Http\Resources\Api\V1\Admin\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopUpByCustomerReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		return [
			'customer' => $this->customer . ' (' . $this->phone . ')',
			'total_transaction' => $this->total_transaction,
			'total_amount' => number_format($this->total_amount, 0) . ' KHR',
		];
    }
}

