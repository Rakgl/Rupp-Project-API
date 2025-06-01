<?php

namespace App\Http\Resources\Api\V1\Mobile\AppVersion;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppVersionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$locale = $request->locale ? $request->locale : 'en';
		$message = $this->message ? json_decode($this->message, true) : null;

        return [
			'platform' => $this->platform,
			'latest_version' => $this->latest_version,
			'force_update' => $this->force_update,
			'url' => $this->update_url,
			'message' => $message && isset($message[$locale]) ? $message[$locale] : '',
		];
    }
}
