<?php

namespace Tests\Unit;

use App\ClientDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_value_types_must_return_true_value()
    {
        $this->assertEquals(ClientDetail::getValuesType('NATURAL'), 'حقیقی');
        $this->assertEquals(ClientDetail::getValuesType('LEGAL'), 'حقوقی');
        $this->assertEquals(ClientDetail::getValuesType('COMPANY'), 'شرکتی');
    }
}
