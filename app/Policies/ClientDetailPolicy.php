<?php

namespace App\Policies;

use App\ClientDetail;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Morilog\Jalali\Jalalian;

class ClientDetailPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        if ($user->type == 'CLIENT' && $user->mobile_verified_at != null) {
            return true;
        } else {
            return false;
        }
    }

    public function verifyMobile(User $user)
    {
        // dd($user);

        return true;
    }

    public function takeWorkGroups(User $user, ClientDetail $detail)
    {
        // check if work group in request is bigger than subscription_count make error
        

        return true;
    }
}
