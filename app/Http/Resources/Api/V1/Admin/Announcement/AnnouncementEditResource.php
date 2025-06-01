<?php

namespace App\Http\Resources\Api\V1\Admin\Announcement;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use function PHPUnit\Framework\isEmpty;

class AnnouncementEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
		$appVersions = $this->appVersions()->get();
		
		$platform = count($appVersions) > 0 ? $appVersions[0]->platform : null;	
		if(count($appVersions) > 1) {
			$platform = 'ALL';
		}
		$appVersion = count($appVersions) > 0 ? $appVersions[0] : null;
		$forceUpdate = $appVersion && $appVersion->force_update ? 'YES' : 'NO';

		$iosUrl = $appVersions->where('platform', 'IOS')->first();
		$androidUrl = $appVersions->where('platform', 'ANDROID')->first();
		return [
			'id' => $this->id,
			'title' => json_decode($this->title, true),
			'message' => json_decode($this->message, true),
			'type' => $this->type,
			'scheduled_at' => $this->scheduled_at,
			'status' => $this->status,
			'image' => Helper::imageUrl($this->image),
			'sent_at' => $this->sent_at,
			'sent_by' => $this->sent_by,
			'platform' => $platform,
			'android_url' => $androidUrl ? $androidUrl->update_url : null,
			'ios_url' => $iosUrl ? $iosUrl->update_url : null,
			'force_update' => $forceUpdate,
			'version' => $appVersion ? $appVersion->latest_version : null,
		];
    }
}
