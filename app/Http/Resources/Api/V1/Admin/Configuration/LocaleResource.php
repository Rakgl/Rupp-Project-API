<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class LocaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $status = "";
        if($this->status === 1){
            $status = "Active";
        }else if($this->status === 2){
            $status = "Inactive";
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'iso' => $this->iso,
            'default' => $this->default ? true : false,
            'status' => $status,
        ];
    }
}
