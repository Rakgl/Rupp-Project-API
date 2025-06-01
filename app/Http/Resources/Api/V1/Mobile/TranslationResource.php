<?php

namespace App\Http\Resources\Api\V1\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		// $locale = $request->header('locale') ? $request->header('locale') : 'en';
		$locale = $request->locale ? $request->locale : 'en';
		$value = json_decode($this->value);
        return [
            'key' => $this->key,
            'value' => $value->$locale,
		];
    }
}
