<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdvertiseWorkGroup extends Model
{
    public function workGroups()
    {
        return $this->belongsToMany(WorkGroup::class);
    }

    public function advertise()
    {
        return $this->belongsToMany(Advertise::class);
    }
}
