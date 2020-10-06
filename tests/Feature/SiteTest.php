<?php

namespace Tests\Feature;

use App\Advertise;
use App\Subscription;
use App\User;
use App\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function return_all_published_parent_work_group()
    {
        create(WorkGroup::class, 5, [
            'status' => 1,
            'parent_id' => null
        ]);
        create(WorkGroup::class, 5, [
            'status' => 1,
            'parent_id' => 1
        ]);
        $response = $this->call('GET', 'api/site/parent/workgroups')->json();
        $this->assertEquals(5, count($response['data']));
    }

    /** @test */
    public function an_user_and_admin_can_search_through_the_advertises_in_whole_of_columns()
    {
        $advertises = create(Advertise::class, 1, [
            'title' => 'dick'
        ]);
        $advertises = create(Advertise::class, 1, [
            'tender_code' => '1632'
        ]);
        create(Advertise::class, 5);
        $advertises = create(Advertise::class, 5);
        $searchTerm = '1632';
        $datas = $this->call('GET', 'api/site/advertise?searchTerm=' . $searchTerm)->json();
        $this->assertEquals(1, count($datas['data']));
    }

    /** @test */
    public function all_functionality_for_user_profile()
    {
        //create a user
        $data = [
            'mobile' => '09101547528',
            'name' => 'Alireza',
        ];
        $this->call('POST', 'api/site/initial/register', $data)
        ->assertStatus(200);
        $data = [
            'mobile' => '09101547528',
            'name' => 'Alireza',
            'registration_code' => '021051',
        ];
        $this->call('POST', 'api/site/register', $data);
        $this->assertDatabaseHas('users', [
            'name' => 'Alireza'
        ]);
        $this->assertTrue(Auth::check());
        $this->assertDatabaseHas('client_details', [
            'user_id' => 1
        ]);
        $data = [
            'mobile' => '09101547528',
            'password' => '021051',
        ];
        // login
        $this->call('POST', 'api/site/login', $data);

        // retrive and see all datas
        $this->call('GET', 'api/site/user')->json();

        // update user profiles

        $data = [
            'mobile' => '09121547528',
            'name' => 'new alireza',
            'phone' => '66456255',
            'company_name' => '66456255',
            'type' => 'NATURAL',
        ];
        $this->call('POST', 'api/site/profile', $data)->assertStatus(200);
        // check is updated
        $this->assertEquals(auth()->user()->detail->type, $data['type']);
        $this->assertEquals(auth()->user()->detail->company_name, $data['company_name']);
        $this->assertEquals(auth()->user()->detail->phone, $data['phone']);
        $this->assertEquals(auth()->user()->name, $data['name']);
        $this->assertEquals(auth()->user()->mobile, $data['mobile']);
        // create subscription
        $subscription = create(Subscription::class, 1, ['cost' => 0, 'allowed_selection' => 30])->first();
        // we need take work groups from user
        $this->call('POST', 'api/site/user/take/subscription/' . $subscription->id)->assertStatus(200);
        create(WorkGroup::class, 2);
        $childrenWorkGroups = create(WorkGroup::class, 20);
        for ($i = 3; $i < 13; $i++) {
            $newWk = WorkGroup::where('id', $i)->first();
            $newWk->parent_id = 1;
            $newWk->save();
        }

        for ($i = 13; $i < 23; $i++) {
            $newWk = WorkGroup::where('id', $i)->first();
            $newWk->parent_id = 2;
            $newWk->save();
        }
        $data = [
            'work_groups' => $childrenWorkGroups->pluck('id')
        ];

        $this->call('POST', 'api/site/user/take/workgroups', $data)->assertStatus(200);
        $this->assertEquals(22, count(DB::table('work_groups_client_detail')->get()));
        $this->assertEquals(22, count($this->call('GET', 'api/site/user', $data)->json()['data']['work_groups']));

        // here we need an api to retrive all work groups that the user selected
        $this->call('GET', 'api/site/user/workGroups')->json();

        // we need to get all work groups
        $this->call('GET', 'api/site/all/workGroups')->json();
    }

    /** @test */
    public function an_user_can_change_password()
    {
        $data = [
            'mobile' => '09101547528',
            'name' => 'Alireza',
        ];
        $this->call('POST', 'api/site/initial/register', $data)
    ->assertStatus(200);
        $data = [
            'mobile' => '09101547528',
            'name' => 'Alireza',
            'registration_code' => '021051',
        ];
        $this->call('POST', 'api/site/register', $data);
        $this->assertDatabaseHas('users', [
            'name' => 'Alireza'
        ]);
        $this->assertTrue(Auth::check());
        $this->assertDatabaseHas('client_details', [
            'user_id' => 1
        ]);
        $data = [
            'mobile' => '09101547528',
            'password' => '021051',
        ];
        // login
        $this->call('POST', 'api/site/login', $data);
        $newData = [
            'mobile' => '09101547528',
            'password' => '021051021051',
        ];

        $this->call('POST', 'api/site/change/password', $newData);

        $this->call('POST', 'api/site/logout')->assertStatus(200);
        $this->assertFalse(Auth::check());
        $this->call('POST', 'api/site/login', $newData);
        $this->assertTrue(Auth::check());
    }

    /** @test */
    public function a_user_can_retrive_her_or_his_password_when_forgotten()
    {
        // we should have forget password api
        $data = [
            'mobile' => '09101547528',
            'name' => 'Alireza',
        ];
        $this->call('POST', 'api/site/initial/register', $data)
    ->assertStatus(200);
        $data = [
            'mobile' => '09101547528',
            'name' => 'Alireza',
            'registration_code' => '021051',
        ];
        $this->call('POST', 'api/site/register', $data);
        $this->assertDatabaseHas('users', [
            'name' => 'Alireza'
        ]);

        $user = User::all()->first();

        $data = [
            'mobile' => $user->mobile
        ];
        $this->call('POST', 'api/site/logout', $data);
        $this->assertFalse(Auth::check());
        $this->call('POST', 'api/site/forget/password', $data)->assertStatus(200);

        $data = [
            'mobile' => $user->mobile,
            'password' => '2087596'
        ];

        $this->call('POST', 'api/site/login', $data)
        ->assertStatus(200);
        $this->assertTrue(Auth::check());
    }
}
