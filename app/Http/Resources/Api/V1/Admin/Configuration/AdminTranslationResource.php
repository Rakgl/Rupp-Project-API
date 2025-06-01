<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminTranslationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $translateValue = json_decode($this->value, true);

        $data = "";
        if($request->header('locale')){
            $data = isset($translateValue[$request->header('locale')]) ? [$request->header('locale') => $translateValue[$request->header('locale')]] : [];
        }else{
            $data = $translateValue;
        }

        return [
            'id' => $this->id,
            'translate_key' => $this->key,
            'translate_value' => $data,
        ];
    }
}
