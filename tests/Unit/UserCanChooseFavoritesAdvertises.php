<?php

namespace Tests\Unit;

use App\Advertise;
use App\ClientDetail;
use App\Subscription;
use App\User;
use App\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCanChooseFavoritesAdvertises extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_user_can_mark_as_favorite()
    {
        $detail = create(ClientDetail::class, 1, [], 'verified_mobile')->first();
        $user = $detail->user;
        $this->signIn($user);
        $subscription = create(Subscription::class, 1, ['allowed_selection' => 2], 'free')->first();
        $this->call('POST', 'api/site/user/take/subscription/' . $subscription->id)
        ->assertStatus(200);
        // make error if user have not a subscription or expired plane or no workgroup count return 403 status
        create(WorkGroup::class);
        $workGroups = create(WorkGroup::class, 20, ['parent_id' => 1]);
        $work_groups = $workGroups->pluck('id');

        $data = [
            'work_groups' => $work_groups
        ];
        $this->call('POST', 'api/site/user/take/workgroups', $data);
        // we have work groups here on our client detail
        // and we want can set this advertises as favorites
        $advertise = create(Advertise::class, 1, ['status' => '1'])->first();
        $workGroupsId = WorkGroup::all()->pluck('id');
        $advertise->workGroups()->sync($workGroupsId);
        $this->call('POST', 'api/site/user/favorite/' . $advertise->id);
        $this->assertCount(1, $detail->favorites);
        $this->call('POST', 'api/site/user/favorite/' . $advertise->id);
        $this->assertCount(0, $detail->fresh()->favorites);
        $this->call('POST', 'api/site/user/favorite/' . $advertise->id);
        $this->assertCount(1, $detail->fresh()->favorites);
        $this->assertCount(1, $this->call('GET', 'api/site/user/favorite/advertises')->json()['data']);
    }
}
