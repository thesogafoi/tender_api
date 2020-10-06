<?php

namespace Tests\Feature;

use App\ClientDetail;
use App\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class subscriptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_subscription_can_be_created()
    {
        $data = [
            'allowed_selection' => '20',
            'cost' => '100',
            'period' => '180',
            'priorty' => 1,
            'title' => 'some plane is here',
            'status' => 0,
        ];
        $this->json('POST', 'api/subscription', $data)
        ->assertStatus(200);
        $this->assertEquals(1, count(Subscription::all()));
        $this->assertDatabaseHas('subscriptions', [
            'cost' => $data['cost']
        ]);
    }

    /** @test */
    public function make_subscription_publish_and_unpublish()
    {
        $subscription = create(Subscription::class)->first();
        $this->call('POST', 'api/subscription/publish/' . $subscription->id)
        ->assertStatus(200);
        $this->assertEquals(1, $subscription->fresh()->status);
        $this->call('POST', 'api/subscription/unpublish/' . $subscription->id);

        $this->assertEquals(0, $subscription->fresh()->status);
    }

    /** @test */
    // public function a_subscription_plane_can_created_and_a_client_can_pay_it_and_get_expire_date_and_selection_allowed()
    // {
    //     $client = create(ClientDetail::class, 1, [], 'verified_mobile')->first();
    //     $subscription = create(Subscription::class)->first();
    // }

    /** @test */
    public function a_subscription_can_has_active_deactive_functionality()
    {
        // create a subscription
        $subscription = create(Subscription::class)->first();
        // test deactive for first time
        $this->assertEquals(0, $subscription->status);
        // activate it
        $subscription->active();
        // test for activate
        $this->assertEquals(1, $subscription->status);
        // deactive it
        $subscription->deactive();
        // test for deactive
        $this->assertEquals(0, $subscription->status);
    }

    /** @test */
    public function a_subscription_plane_can_updated()
    {
        $data = [
            'allowed_selection' => '20',
            'cost' => '100',
            'period' => '180',
            'priorty' => 1,
            'title' => 'some plane is here',
            'status' => 1
        ];
        $this->json('POST', 'api/subscription', $data)->assertStatus(200);
        $this->assertEquals(1, count(Subscription::all()));
        $this->assertDatabaseHas('subscriptions', [
            'cost' => $data['cost']
        ]);
        $data = [
            'allowed_selection' => '5',
            'cost' => '1000',
            'period' => '200',
            'priorty' => 1,
            'title' => 'another title',
            'status' => 0
        ];
        $this->json('PUT', 'api/subscription/1', $data)->assertStatus(200);
        $this->assertEquals('5', Subscription::all()->last()->allowed_selection);
        $this->assertEquals('1000', Subscription::all()->last()->cost);
        $this->assertEquals('200', Subscription::all()->last()->period);
        $this->assertEquals('1', Subscription::all()->last()->priorty);
        $this->assertEquals('another title', Subscription::all()->last()->title);
    }

    /** @test */
    public function a_subscription_can_deleted()
    {
        $subscription = create(Subscription::class)->first();
        $this->assertEquals(1, count(Subscription::all()));
        $this->call('DELETE', 'api/subscription/' . $subscription->id)
        ->assertStatus(200);
        $this->assertEquals(0, count(Subscription::all()));
    }
}
