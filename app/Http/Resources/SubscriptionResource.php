<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'title' => $this->title,
            'cost' => $this->cost,
            'period' => $this->period,
            'status' => (int) $this->status,
            'priorty' => $this->priorty,
            'allowed_selection' => $this->allowed_selection,
            'created_at' => $this->created_at,
        ];
    }
}
