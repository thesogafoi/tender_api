<?php

namespace App;

use App\Traits\StatusTrait;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Subscription extends Model
{
    use StatusTrait;

    /* *********************
    *   Searchable Area
    ************************/
    public function shouldBeSearchable()
    {
        return !!$this->status != 0;
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();

        return $array;
    }
}
