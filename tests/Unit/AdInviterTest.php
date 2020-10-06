<?php

namespace Tests\Unit;

use App\AdInviter;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdInviterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    // public function just_super_admin_can_add_adinviter()
    // {
    //     $user = create(User::class, 1, [], 'admin');
    //     $this->signIn($user);
    //     $data = [
    //         'name' => 'شرکت برق'
    //     ];

    //     $this->post('api/adinviter/create', $data)
    //     ->assertStatus(403);
    //     $this->assertCount(0, AdInviter::all());
    //     $user = create(User::class, 1, [], 'superadmin');
    //     $this->signIn($user);
    //     $this->post('api/adinviter/create', $data);
    //     $this->assertCount(1, AdInviter::all());
    // }

    /** @test */
    public function ad_inviter_can_created()
    {
        $user = create(User::class, 1, [], 'superadmin');
        $this->signIn($user);
        $data = [
            'name' => 'شرکت برق'
        ];
        $this->post('api/adinviter/create', $data);
        $this->assertDatabaseHas('ad_inviters', [
            'name' => $data['name']
        ]);
    }
}
