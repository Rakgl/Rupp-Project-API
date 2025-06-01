<?php

namespace App\Http\Resources\Api\V1\Admin\Faq;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FAQIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$locale = $request->locale ? $request->locale : 'en';
		$question = json_decode($this->question, true);
		$answer = json_decode($this->answer, true);
        return [
			'id' => $this->id,
			'question' => $question ? $question['en'] : $this->question,
			'answer' => strip_tags($answer[$locale]),	
			'category' => $this->category,
			'status' => Helper::formatStatus($this->status),
		];
    }
}
