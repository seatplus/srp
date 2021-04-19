<?php


namespace Unit;


use Seatplus\Srp\Models\SrpRequest;
use Seatplus\Srp\Tests\TestCase;

class SrpRequestTest extends TestCase
{
    /** @test */
    public function test()
    {

        SrpRequest::factory()->create();

        dd(SrpRequest::first()->killmail_array);

        $this->assertTrue(true);
    }

}