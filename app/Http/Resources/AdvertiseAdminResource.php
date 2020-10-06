<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class AdvertiseAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tender_code' => $this->tender_code,
            'invitation_code' => $this->invitation_code,
            'title' => $this->title,
            'adinviter_title' => $this->adinviter_title,
            'status' => $this->status,
            'invitation_date' => Jalalian::forge($this->invitation_date)->format('Y-m-d'),
            'created_at' => Jalalian::forge($this->created_at)->format('Y-m-d'),
            'free_date' => Jalalian::forge($this->free_date)->format('Y-m-d'),
            'resource' => $this->resource->resource,
            'work_groups' => $this->workGroups,
        ];
    }
}
