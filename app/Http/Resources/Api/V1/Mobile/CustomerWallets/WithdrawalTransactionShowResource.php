<?php

namespace App\Http\Resources\Api\V1\Mobile\CustomerWallets;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalTransactionShowResource extends JsonResource
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
			'processed_by' => $this->processedBy ? $this->processedBy->name : null,
			'notes' => $this->notes ?? 'N/A',
			'title' => 'Withdrawal'
		];
    }
}
