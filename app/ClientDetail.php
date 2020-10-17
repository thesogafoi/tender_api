<?php

namespace App;

use App\Traits\TypesTrait;
use Illuminate\Database\Eloquent\Model;

class ClientDetail extends Model
{
    use TypesTrait;

    /* *********************
    *      Enum Types
    ************************/
    const TYPEDEFINITION = ['NATURAL' => 'حقیقی', 'LEGAL' => 'حقوقی', 'COMPANY' => 'شرکتی'];
    const TYPES = ['NATURAL', 'LEGAL', 'COMPANY'];

    /* *********************
    *    Relation Ships
    ************************/
    public function workGroups()
    {
        return $this->belongsToMany(WorkGroup::class, 'work_groups_client_detail');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function planes()
    {
        return $this->hasMany(ClientDetailPlane::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Advertise::class, 'client_detail_favorite_advertise');
    }
}
