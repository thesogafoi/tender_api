<?php

namespace Tests\Unit;

use App\Advertise;
use App\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAdvertiseInSite extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function site_can_get_last_five_advertise()
    {
        create(Advertise::class, 6);
        create(Province::class, 5);
        $getProvincesId = Province::all()->pluck('id');

        foreach (Advertise::all() as $value) {
            $value->provinces()->sync($getProvincesId);
        }
        $response = $this->call('GET', 'api/site/advertise?get_last_five=true')->json();
        $this->assertEquals(5, count($response['data']));
    }
}
