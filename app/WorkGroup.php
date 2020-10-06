<?php

namespace App;

use App\Traits\StatusTrait;
use App\Traits\TypesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkGroup extends Model
{
    use TypesTrait , StatusTrait , SoftDeletes;

    /* *********************
    *      Enum Types
    ************************/

    const TYPEDEFINITION = ['AUCTION' => 'مزایده', 'TENDER' => 'مناقصه', 'INQUIRY' => 'استعلام'];
    const TYPES = ['AUCTION', 'TENDER', 'INQUIRY'];

    protected $fillable = ['title'];

    /* *********************
    *    Relation Ships
    ************************/
    public function advertises()
    {
        return $this->belongsToMany(Advertise::class, 'advertise_work_group');
    }

    public function parent()
    {
        return $this->belongsTo(WorkGroup::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(WorkGroup::class, 'parent_id');
    }

    public function scopeFilterByArray($query, $array)
    {
        
    }
}
