<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class workGroupIndexRecourse extends JsonResource
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
            'parent_id' => $this->parent_id,
            'type' => $this->type,
            'title' => $this->title,
            'image' => $this->image,
            'status' => $this->status,
            'priorty' => $this->priorty,
            'children' => $this->children,
        ];
    }
}
