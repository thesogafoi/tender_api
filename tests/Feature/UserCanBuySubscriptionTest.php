<?php

namespace Tests\Feature;

use App\ClientDetail;
use App\Subscription;
use App\User;
use App\WorkGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Morilog\Jalali\Jalalian;
use Tests\TestCase;

class UserCanBuySubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_take_a_plane()
    {
        $detail = create(ClientDetail::class, 1, [], 'verified_mobile')->first();

        $this->signIn($detail->user);
        $subscription = create(Subscription::class, 1, [], 'free')->first();
        $this->call('POST', 'api/site/user/take/subscription/' . $subscription->id)->assertStatus(200);
        $this->assertEquals(Jalalian::fromCarbon($subscription->period)->format('Y-m-d'), Jalalian::fromCarbon(auth()->user()->detail->subscription_date)->format('Y-m-d'));
        $this->assertEquals($detail->fresh()->subscription_title, $subscription->title);
        $this->assertEquals($detail->fresh()->subscription_count, $subscription->allowed_selection);
    }

    /** @test */
    public function a_user_can_take_work_groups()
    {
        $detail = create(ClientDetail::class, 1, [], 'verified_mobile')->first();
        $this->signIn($detail->user);
        $subscription = create(Subscription::class, 1, [], 'free')->first();
        $this->call('POST', 'api/site/user/take/subscription/' . $subscription->id)
        ->assertStatus(200);
        // make error if user have not a subscription or expired plane or no workgroup count return 403 status
        $workGroups = create(WorkGroup::class, 2);
        $children = create(WorkGroup::class, 20);
        for ($i = 3; $i < 13; $i++) {
            $workGroup = WorkGroup::where('id', $i)->first();
            $workGroup->parent_id = 1;
            $workGroup->save();
        }

        for ($i = 13; $i < 23; $i++) {
            $workGroup = WorkGroup::where('id', $i)->first();
            $workGroup->parent_id = 2;
            $workGroup->save();
        }
        $work_groups = WorkGroup::where('parent_id', '!=', 'null')->get()->pluck('id');

        $data = [
            'work_groups' => $work_groups
        ];
        $this->call('POST', 'api/site/user/take/workgroups', $data);
        $this->assertEquals(count($detail->fresh()->workGroups), 22);
    }

    /** @test */
    public function if_user_has_not_qualify_for_take_work_group_should_response_errors()
    {
        $detail = create(ClientDetail::class, 1, [], 'verified_mobile')->first();
        $user = $detail->user;
        $this->signIn($user);
        $subscription = create(Subscription::class, 1, ['allowed_selection' => 2], 'free')->first();
        $this->call('POST', 'api/site/user/take/subscription/' . $subscription->id)
        ->assertStatus(200);
        // make error if user have not a subscription or expired plane or no workgroup count return 403 status
        create(WorkGroup::class);
        $workGroups = create(WorkGroup::class, 201, ['parent_id' => 1]);
        $work_groups = $workGroups->pluck('id');
        $data = [
            'work_groups' => $work_groups
        ];
        $this->call('POST', 'api/site/user/take/workgroups', $data)
        ->assertStatus(403);
    }
}
