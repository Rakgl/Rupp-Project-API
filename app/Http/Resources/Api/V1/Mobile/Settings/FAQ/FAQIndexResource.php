<?php

namespace App\Http\Resources\Api\V1\Mobile\Settings\FAQ;

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
		$question = json_decode($this->question);
		$answer = json_decode($this->answer);

        return [
			'id' => $this->id,
			'question' => $question->$locale,
			'answer' => $answer->$locale,
			'image' => $this->image,
		];
    }
}
