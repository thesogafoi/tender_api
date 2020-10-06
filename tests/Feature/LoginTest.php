<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_logged_out_user_can_login_to_app_if_user_exists_in_database()
    {
        $user = create(User::class)->first();
        $data = [
            'mobile' => $user->mobile,
            'password' => '021051',
        ];
        $this->post('/api/login', $data)->json();
        $response = $this->post('/api/login', $data);
        $this->assertTrue(Auth::check());
    }

    /** @test */
    public function if_someone_logged_in_and_hit_logout_end_point_should_log_out()
    {
        $user = $this->signIn()->first();
        $userToken = $this->post('api/login', [
            'mobile' => $user->mobile,
            'password' => '021051',
        ]);
        $token = json_decode($userToken->getContent())->token;
        $this->post('/api/logout', [], [
            'Authorization' => "Bearer {$token}"
        ]);
        $this->assertFalse(Auth::check());
    }
}
