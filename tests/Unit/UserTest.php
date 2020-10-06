<?php

namespace Tests\Unit;

use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_value_types_must_return_true_value()
    {
        $this->assertEquals(User::getValuesType('ADMIN'), 'ادمین');
        $this->assertEquals(User::getValuesType('STAFF'), 'عضو تیم آسان تندر');
        $this->assertEquals(User::getValuesType('CLIENT'), 'مشتری');
    }

    /** @test */
    public function an_client_can_active_and_deactive_by_admin()
    {
        $user = create(User::class)->first();
        $this->assertEquals(1, $user->status);
        $user->deactive();
        $this->assertEquals(0, $user->status);
        $user->active();
        $this->assertEquals(1, $user->status);
    }

    /** @test */
    public function a_superadmin_can_login_with_existed_data_in_data_base()
    {
        $user = factory(User::class)->create([
            'name' => 'کهشیدی',
            'mobile' => '09120751179',
            'type' => 'SUPERADMIN',
            'mobile_verified_at' => Carbon::now(),
            'password' => Hash::make('021051'),
            'status' => 1,
        ]);
        $registeredSuperAdmingData = [
            'mobile' => $user->mobile,
            'password' => '021051',
        ];
        $this->call('post', 'api/login', $registeredSuperAdmingData)
        ->assertStatus(200);
    }
}
