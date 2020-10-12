<?php

namespace App;

use App\Traits\StatusTrait;
use App\Traits\TypesTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable , TypesTrait , StatusTrait;

    /* *********************
    *      Enum Types
    ************************/
    const TYPEDEFINITION = ['SUPERADMIN' => 'ادمین اصلی', 'ADMIN' => 'ادمین', 'STAFF' => 'عضو تیم آسان تندر', 'CLIENT' => 'مشتری'];
    const TYPES = ['SUPERADMIN', 'ADMIN', 'STAFF', 'CLIENT'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'mobile', 'password', 'mobile_verified_at'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'mobile_verified_at' => 'datetime',
    ];
    /* *********************
    *   JWT CONFIGURATION
    ************************/

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /* *********************
    *      Relations
    ************************/

    public function detail()
    {
        return $this->hasOne(ClientDetail::class);
    }

    public function getRouteKeyName()
    {
        return 'mobile';
    }

    /* *********************
    *     Custom Functions
    ************************/

    public function isSuperAdmin()
    {
        return 'SUPERADMIN' == $this->type;
    }

    public function isAdmin()
    {
        return 'isAdmin' == $this->type;
    }

    public function isStaff()
    {
        return 'STAFF' == $this->type;
    }

    public function isClient()
    {
        return 'CLIENT' == $this->type;
    }

    public static function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}
