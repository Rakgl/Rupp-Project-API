<?php

namespace App\Http\Resources\Api\V1\Mobile\CustomerWallets;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerTransactionIndexResource extends JsonResource
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
			'amount' => number_format($this->amount, 2, '.', ','),
			'payment_method' => $this->payment_method,
			'status' => $this->status,
			'type' => $this->type,
			'date' => Helper::formatDate($this->created_at),
			'title' => $this->title
		];
    }
}
