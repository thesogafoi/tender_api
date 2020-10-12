<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class ClientDetailIndexAdminAreaResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $clientDetail = $this->detail;

        return [
            'client_code' => $clientDetail->id,
            'client_name' => $this->name,
            'company_name' => $clientDetail->company_name,
            'mobile' => $this->mobile,
            'register_date' => Jalalian::fromCarbon($clientDetail->created_at)->format('Y-m-d'),
            'user_type' => $clientDetail->type,
            'tel' => $clientDetail->phone,
        ];
    }
}
