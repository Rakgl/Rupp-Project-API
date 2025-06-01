<?php

namespace App\Http\Resources\Api\V1\Admin\CustomerTopUp;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerTopUpShowResource extends JsonResource
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
			'transaction_no' => $this->transaction_no,
			'name' => $this->name,
			'phone' => $this->phone,
			'created_at' => Helper::formatDate($this->created_at),
			'amount' => number_format($this->amount, 0) . ' KHR',
			'payment_method' => $this->payment_method,
			'status' => $this->status,
			'type' => $this->type,
			'description' => $this->description,
			'created_by' => $this->createdBy ? $this->createdBy->name : null,
			'payway_check_at' => $this->payway_check_at,
			'payway_pushback_at' => $this->payway_pushback_at,
		];
    }
}
