<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
            'can_client_see_advertise' => Carbon::now()->greaterThanOrEqualTo(Carbon::parse($this->free_date)) ? true : Gate::allows('client-can-see-advertise', $this)
        ];
    }
}
