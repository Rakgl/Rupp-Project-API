<?php

namespace App\Http\Resources\Api\V1\Admin\ReportIssue;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportIssueShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$issueImages = $this->images()->get();
		$images = [];
		foreach ($issueImages as $issueImage) {
			$images[] = Helper::imageUrl($issueImage->image);
		}
        return [
			'images' => $images,
			'message' => $this->message,
			'station' => $this->station ? $this->station->name : null,
			'date' => Helper::formatDate($this->created_at),
			'report_by' => $this->customer? $this->customer->phone : null,
			'status' => $this->status,
			'resolved_by' => $this->resolvedBy	? $this->resolvedBy->name : null,
		];
    }
}
