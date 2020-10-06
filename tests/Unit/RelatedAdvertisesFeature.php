<?php

namespace Tests\Unit;

use App\Advertise;
use App\ClientDetail;
use App\Subscription;
use App\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatedAdvertisesFeature extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_see_related_advertises_in_show_page()
    {
        // create advertises
        $advertises = create(Advertise::class, 20);
        // create workGroups
        create(WorkGroup::class, 3);
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
        $this->signIn($clientDetail->user);
        // take a plane from user
        $this->call('POST', 'api/site/user/take/subscription/' . $plan->id);

        // assign workGroups to user
        $data = [
            'work_groups' => $workGruopsId
        ];
        $this->call('POST', 'api/site/user/take/workgroups', $data);
        // check advertises
        create(Advertise::class, 10);
        $advertise = Advertise::where('id', 1)->first();
        $this->assertCount(2, $this->call('GET', 'api/site/user/related/advertises/' . $advertise->id)->json());
    }
}
