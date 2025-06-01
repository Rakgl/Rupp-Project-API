<?php

namespace App\Http\Resources\Api\V1\Admin\CustomerWithdrawal;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerWithdrawalShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {	
        return [
			'date' => Helper::formatDate($this->created_at),
			'transaction_no' => $this->transaction_no,
			'name' => $this->name,
			'phone' => $this->phone,
			'amount' => number_format($this->amount, 0) . ' KHR',
			'status' => $this->status,
			'requested_at' => Helper::formatDate($this->requested_at),
			'processed_at' => Helper::formatDate($this->processed_at),
			'processed_by' => $this->processedBy ? $this->processedBy->name : null,
			'requested_by' => $this->requested_by ? $this->requestedBy->name : null,
		];
    }
}
