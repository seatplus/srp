<?php


namespace Seatplus\Srp\Tests\Integration;

use Illuminate\Support\Facades\Bus;
use Inertia\Testing\AssertableInertia;
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
            ->post(route('store.srp.request'), [
                'killmailUrl' => 'https://esi.evetech.net/latest/killmails/92281357/19c919549fb5b4359324fc7938b21f2965f1baf0/',
            ])->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Srp/Processing'));

        $this->assertNotEmpty(SrpRequest::all());

        Bus::assertBatched(fn ($batch) => $batch->jobs->first(fn ($job) => $job instanceof KillmailJob));
    }

    /** @test */
    public function oneCanDeleteOpenOwnedSRPRequests()
    {
        $request = SrpRequest::factory()->create([
            'user_id' => $this->test_user->id,
        ]);

        $this->assertEquals($this->test_user->id, $request->refresh()->user_id);
        $this->assertEquals('open', $request->refresh()->status);

        $response = $this->actingAs($this->test_user)
            ->delete(route('delete.srp.request', $request->refresh()->id))
            ->assertRedirect();
    }

    /** @test */
    public function oneCanNotDeleteSubmittedOwnedSRPRequests()
    {
        $request = SrpRequest::factory()->create([
            'user_id' => $this->test_user->id,
            'status' => 'submitted',
        ]);

        $this->assertEquals($this->test_user->id, $request->refresh()->user_id);
        $this->assertEquals('submitted', $request->refresh()->status);

        $response = $this->actingAs($this->test_user)
            ->delete(route('delete.srp.request', $request->refresh()->id))
            ->assertForbidden();
    }

    /** @test */
    public function oneCanNotDeleteOpenForeignSRPRequests()
    {
        $request = SrpRequest::factory()->create();

        $this->assertNotEquals($this->test_user->id, $request->refresh()->user_id);
        $this->assertEquals('open', $request->refresh()->status);

        $response = $this->actingAs($this->test_user)
            ->delete(route('delete.srp.request', $request->refresh()->id))
            ->assertForbidden();
    }
}
