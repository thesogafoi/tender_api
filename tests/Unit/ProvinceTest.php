<?php

namespace Tests\Unit;

use App\Province;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProvinceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_province_can_created()
    {
        $data = [
            'name' => 'اصفهان'
        ];
        $this->json('POST', 'api/province/create', $data)->assertStatus(403);
        $user = create(User::class, 1, [], 'superadmin');
        $this->signIn($user);
        $this->json('POST', 'api/province/create', $data)->assertStatus(200);
    }

    /** @test */
    public function a_province_can_updated()
    {
        $province = create(Province::class)->first();
        $data = [
            'name' => 'new city'
        ];
        $user = create(User::class, 1, [], 'superadmin');
        $this->signIn($user);
        $this->call('POST', 'api/province/update/' . $province->id, $data);

        $this->assertDatabaseHas('provinces', [
            'name' => 'new city'
        ]);
    }
}
