<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRecourses extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return[
            'name' => $this->name,
            'mobile' => $this->mobile,
            'type' => $this->type,
            'mobile_verified_at' => $this->mobile_verified_at
        ];
    }
}
