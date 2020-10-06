<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_created()
    {
        $data = [
            'name' => 'theosgafoi',
            'mobile' => '09101547528',
        ];
        $this->post('api/site/initial/register', $data)->json();

        $data = [
            'name' => 'theosgafoi',
            'mobile' => '09101547528',
            'registration_code' => '021051'
        ];
        $this->call('POST', '/api/site/register', $data)->assertStatus(201);
    }
}
