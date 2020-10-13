<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class ShowAdvertiseResource extends JsonResource
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
            'adinviter_title' => $this->adinviter_title,
            'description' => $this->description,
            'id' => $this->id,
            'image' => $this->image,
            'is_nerve_center' => (string) $this->is_nerve_center ? 1 : 0,
            'link' => $this->link,
            'invitation_code' => $this->invitation_code,
            'invitation_date' => $this->invitation_date != null ? Jalalian::forge($this->invitation_date)->format('Y-m-d') : null,
            'free_date' => $this->free_date != null ? Jalalian::forge($this->free_date)->format('Y-m-d') : null,
            'receipt_date' => $this->receipt_date != null ? Jalalian::forge($this->receipt_date)->format('Y-m-d') : null,
            'start_date' => $this->start_date != null ? Jalalian::forge($this->start_date)->format('Y-m-d') : null,
            'submit_date' => $this->submit_date != null ? Jalalian::forge($this->submit_date)->format('Y-m-d') : null,
            'created_at' => $this->created_at != null ? Jalalian::forge($this->created_at)->format('Y-m-d') : null,
            'resource' => $this->resource->resource,
            'status' => (string) $this->status,
            'tender_code' => $this->tender_code,
            'title' => $this->title,
            'type' => $this->type,
            'work_groups' => $this->workGroups->where('parent_id', '!=', null)->pluck('id'),
            'provinces' => $this->provinces->pluck('id'),
        ];
    }
}
