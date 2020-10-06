<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AdvertiseWokrGroup extends Pivot
{
    protected $fillable = ['advertise_id', 'work_group_id'];
}
