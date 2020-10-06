<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    /* *********************
    *    Relation Ships
    ************************/
    public function advertises()
    {
        return $this->belongsToMany(Advertise::class, 'advertise_province', 'province_id', 'advertise_id');
    }
}
