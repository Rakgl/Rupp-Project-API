<?php

namespace App\Http\Resources\Api\V1\Admin\ReportIssue;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportIssueIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$issueImage = $this->images()->count() > 0 ? $this->images()->first() : null;
        return [
			'id' => $this->id,
			'image' => Helper::imageUrl($issueImage?->image),
			'message' => $this->message,
			'date' => Helper::formatDate($this->created_at),
			'report_by' => $this->customer	? $this->customer->phone : null,
			'status' => Helper::formatStatus($this->status),
			'station' => $this->station ? $this->station->name : null
		];
    }
}
