<?php

namespace App\Http\Resources\Api\V1\Admin\CustomerTopUp;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerTopUpIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {	
		$status = Helper::formatStatus($this->status);
        return [
			'id' => $this->id,
			'transaction_no' => $this->transaction_no,
			'name' => $this->name,
			'phone' => $this->phone,
			'created_at' => Helper::formatDate($this->created_at),
			'amount' => number_format($this->amount, 0) . ' KHR',
			'payment_method' => $this->payment_method,
			'status' => $status,
			'payway_check_at' => $this->payway_check_at,
			'payway_pushback_at' => $this->payway_pushback_at,
			'type' => $this->type,
		];
    }
}
