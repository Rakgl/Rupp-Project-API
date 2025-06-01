<?php

namespace App\Http\Resources\Api\V1\Admin\Translation;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslationShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$value = json_decode($this->value);
		$value = json_decode(json_encode($value), true);
		$value = array_values($value);
        return [
			'id' => $this->id,
			'key' => $this->key,
			'value' => $value,
			'platform' => $this->platform,
			'status' => $this->status,
			'created_at' => Helper::formatDate($this->created_at),
		];
    }
}
