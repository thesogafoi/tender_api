<?php

namespace Tests\Unit;

use App\Advertise;
use App\ClientDetail;
use App\Subscription;
use App\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCanSeeThisAdvertiseFeature extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_user_can_see_own_advertise_only()
    {
        // create advertises
        $advertises = create(Advertise::class, 20, ['status' => 1]);
        // create workGroups
        create(WorkGroup::class, 5);
        for ($i = 2; $i <= 5; $i++) {
            $workGroup = WorkGroup::where('id', $i)->first();
            $workGroup->parent_id = 1;
            $workGroup->save();
        }
        $workGruopsId = WorkGroup::all()->pluck('id');

        // assigin some WorkGroups to advertises
        for ($i = 1; $i < 4; $i++) {
            $advertise = Advertise::where('id', $i)->first();
            $advertise->workGroups()->sync($workGruopsId);
        }
        // create a client detail
        $clientDetail = create(ClientDetail::class, 1, [], 'verified_mobile')->first();
        // create a plan
        $plan = create(Subscription::class, 1, ['cost' => 0])->first();
        $token = $this->signInJWT($clientDetail->user);

        // take a plane from user
        $this->call('POST', 'api/site/user/take/subscription/' . $plan->id);

        // assign workGroups to user

        $data = [
            'work_groups' => $workGruopsId
        ];
        unset($data['work_groups'][0]);
        $this->call('POST', 'api/site/user/take/workgroups', $data)->assertStatus(200);
        // check advertises
        // $this->siteLogout($token);

        $response = $this->call('POST', 'api/site/advertise/filter');
        $this->assertTrue($response->json()['data'][0]['can_client_see_advertise']);
        $this->assertFalse($response->json()['data'][8]['can_client_see_advertise']);
    }
}
