<?php

namespace Tests\Unit;

use App\ClientDetail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientDetailSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_client_detail_can_searched()
    {
        // create 3 client detail
        $user1 = create(User::class, 1, [], 'verified_mobile')->first();
        $user2 = create(User::class, 1, [], 'verified_mobile')->first();
        $user3 = create(User::class, 1, [], 'verified_mobile')->first();
        $clientDetail1 = create(ClientDetail::class, 1, ['user_id' => 1])->first();
        $clientDetail2 = create(ClientDetail::class, 1, ['user_id' => 2])->first();
        $clientDetail3 = create(ClientDetail::class, 1, ['user_id' => 3])->first();
        $clientDetail1->user->client_detail_id = 1;
        $clientDetail1->user->save();
        $clientDetail2->user->client_detail_id = 2;
        $clientDetail2->user->save();
        $clientDetail3->user->client_detail_id = 3;
        $clientDetail3->user->save();
        // if has request for search term return data
        $response = $this->get('api/client-detail/index?searchTerm=')->json()['data'];
        $this->assertCount(3, $response);
        // if not return all
    }
}
