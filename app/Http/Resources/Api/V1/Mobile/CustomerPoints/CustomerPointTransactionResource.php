<?php

namespace App\Http\Resources\Api\V1\Mobile\CustomerPoints;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerPointTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$type = $this->type ;
		$title = 'Top up';
		if ($type == 'REDEEMED') {
			$title = 'Redeemed';
		} else if ($type == 'WITHDRAWAL') {
			$title = 'Withdrawal';
		}
        return [
			'id' => $this->id,
			'customer_id' => $this->customer_id,
			'type' => $this->type,
			'points' => $this->points,
			'title' => $title,
			'created_at' => date('d/m/Y , h:i A', strtotime($this->created_at)),
		];
    }
}
