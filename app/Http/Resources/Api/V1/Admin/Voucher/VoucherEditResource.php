<?php

namespace App\Http\Resources\Api\V1\Admin\Voucher;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherEditResource extends JsonResource
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
			'type' => $this->type,
			'code' => $this->code,
			'description' => $this->description,
			'amount' => $this->amount,
			'discount_type' => $this->discount_type,
			'points_required' => $this->points_required,
			'expired_at' => $this->expired_at,
			'formatted_expired_at' => date('d/m/Y , h:i A', strtotime($this->expired_at)),
			'status' => $this->status
		];
    }
}
