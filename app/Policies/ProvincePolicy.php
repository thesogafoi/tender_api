<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProvincePolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->isSuperAdmin();
    }
}
