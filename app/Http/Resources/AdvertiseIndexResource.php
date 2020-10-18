<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Morilog\Jalali\Jalalian;

class AdvertiseIndexResource extends JsonResource
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
            'provinces' => $this->provinces->sortByDesc('id'),
            'created_at' => Jalalian::forge($this->created_at)->format('Y-m-d'),
            'title' => $this->title,
            'type' => $this->type,
            'can_client_see_advertise' => Gate::allows('client-can-see-advertise', $this),

            'has_plane' => Gate::allows('has-plane'),
            'not_choosed_work_groups' => Gate::allows('not-choosed-work-groups'),
            'in_work_groups' => Gate::allows('in-work-groups', $this),
        ];
    }
}
