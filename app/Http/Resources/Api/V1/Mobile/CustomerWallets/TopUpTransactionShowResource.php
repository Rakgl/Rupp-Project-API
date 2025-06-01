<?php

namespace App\Http\Resources\Api\V1\Mobile\CustomerWallets;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopUpTransactionShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		return [
			'transaction_no' => $this->transaction_no,
			'amount' => number_format($this->amount, 2, '.', ','),
			'date' => Helper::formatDate($this->created_at),
			'payment_method' => $this->payment_method,
			'title' => $this->description
		];
    }
}
