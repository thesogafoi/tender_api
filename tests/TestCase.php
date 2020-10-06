<?php

namespace Tests;

use App\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function signIn($user = null)
    {
        if ($user === null) {
            $user = create(User::class);
        }
        $this->actingAs($user->first());

        return $user;
    }

    public function signInJWT($user = null)
    {
        if ($user === null) {
            $user = create(User::class);
        }
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
        $this->actingAs($user->first());

        return $token;
    }

    public function siteLogout($token)
    {
        $this->call('POST', 'api/site/logout?token=' . $token);
    }
}
