<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteUserResource extends JsonResource
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
            'name' => $this->name,
            'mobile' => $this->mobile,
            'status' => $this->status,
            'phone' => $this->detail->phone,
            'type' => $this->detail->type,
            'company_name' => $this->detail->company_name,
            'subscription_date' => $this->detail->subscription_date,
            'subscription_title' => $this->detail->subscription_title,
            'subscription_count' => $this->detail->subscription_count,
            'work_groups_changes' => $this->detail->work_groups_changes,
            'work_groups' => !$this->detail->workGroups->isEmpty() ? $this->detail->workGroups->where('parent_id', '!=', null)->pluck('id') : [],
        ];
    }
}
