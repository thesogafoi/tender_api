<?php

namespace App\Filters;

class ClientFilter extends Filter
{
    public $filters = ['searchTerm'];

    public function searchTerm()
    {
        return $this->builder->where('mobile', '=', $this->request->searchTerm)
        ->orWhere('name', 'LIKE', '%' . $this->request->searchTerm . '%');
    }
}
