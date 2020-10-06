<?php

namespace App;

use App\Traits\StatusTrait;
use App\Traits\TypesTrait;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Advertise extends Model
{
    public static function boot()
    {
        parent::boot();
    }

    use TypesTrait  , StatusTrait;
    protected $guarded = [];

    /* *********************
    *      Enum Types
    ************************/

    const TYPEDEFINITION = ['AUCTION' => 'مزایده', 'TENDER' => 'مناقصه', 'INQUIRY' => 'استعلام'];
    const TYPES = ['AUCTION', 'TENDER', 'INQUIRY'];

    /* *********************
    *   Relation Ships
    ************************/
    public function inviter()
    {
        return $this->belongsTo(AdInviter::class);
    }

    public function provinces()
    {
        return $this->belongsToMany(Province::class, 'advertise_province', 'advertise_id', 'province_id');
    }

    public function workGroups()
    {
        return $this->belongsToMany(WorkGroup::class, 'advertise_work_group');
    }

    public function isPublished()
    {
        return !!$this->status;
    }

    /* *********************
    *   Searchable Area
    ************************/
    public function shouldBeSearchable()
    {
        return !!$this->status != 0;
    }

    public function toSearchableArray()
    {
        // $array = $this->toArray();

        // return $array;

        $array = [
            'tender_code' => $this->tender_code,
            'title' => $this->title,
            'adinviter_title' => $this->adinviter_title,
            'type' => $this->type,
            'invitation_code' => $this->invitation_code,

            'description' => $this->description,
            'resource' => $this->resource,
            'is_nerve_center' => $this->is_nerve_center,
        ];

        return $array;
    }

    /* *********************
    *    Filter Area
    ************************/
    public static function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    /* *********************
    *    Custom Functions
    ************************/
    public function isFavorited()
    {
        if (auth()->user() == null) {
            return false;
        } else {
            if (auth()->user()->detail == null) {
                return false;
            }
            if (auth()->user()->detail->favorites()->where('advertise_id', $this->id)->exists()) {
                return true;
            } else {
                return false;
            }
        }
    }
}
