<?php

namespace App\Http\Resources\Api\V1\Admin\Faq;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FAQEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$question = json_decode($this->question, true);
		$answer = json_decode($this->answer, true);
        return [
			'id' => $this->id,
			'question' => $question,
			'answer' => $answer,
			'category' => $this->category,
			'status' => $this->status
		];
    }
}
