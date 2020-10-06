<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralWorkGroupResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'priorty' => $this->priorty,
            'status' => $this->status,
            'type' => $this->type,
            'image' => $this->image,
        ];
    }
}
