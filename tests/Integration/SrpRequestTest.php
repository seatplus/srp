<?php


namespace Seatplus\Srp\Tests\Integration;


use Illuminate\Support\Facades\Bus;
use Seatplus\Eveapi\Jobs\Killmails\KillmailJob;
use Seatplus\Srp\Models\SrpRequest;
use Seatplus\Srp\Tests\TestCase;

class SrpRequestTest extends TestCase
{
    /** @test */
    public function submissionDispatchesKillmailBatch()
    {
        Bus::fake();

        $this->assertEmpty(SrpRequest::all());

        $this->actingAs($this->test_user)
            ->post(route('store.srp.request'),[
                'killmailUrl' => 'https://esi.evetech.net/latest/killmails/92281357/19c919549fb5b4359324fc7938b21f2965f1baf0/'
            ]);

        $this->assertNotEmpty(SrpRequest::all());

        Bus::assertBatched(fn($batch) => $batch->jobs->first(fn($job) => $job instanceof KillmailJob));
    }
}