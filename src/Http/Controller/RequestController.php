<?php


namespace Seatplus\Srp\Http\Controller;


use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Jobs\Killmails\KillmailJob;
use Seatplus\Eveapi\Models\Killmails\Killmail;
use Seatplus\Srp\Models\SrpRequest;

class RequestController extends Controller
{
    public function index()
    {
        $requests = SrpRequest::query()
            ->with(['killmail' => fn($query) => $query->with('ship', 'system', 'system.region')])
            ->where('user_id', auth()->user()->getAuthIdentifier())
            ->orderBy('created_at')
            ->paginate();

        return inertia('Srp/Request', [
            'requests' => $requests
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'killmailUrl' => ['required', 'string']
        ]);

        $collection = Str::of($request->get('killmailUrl'))
            ->after('https://esi.evetech.net/latest/killmails/')
            ->finish('/')
            ->beforeLast('/')
            ->explode('/');

        $killmail_id = $collection->first();
        $killmail_hash = $collection->last();

        throw_if(SrpRequest::find($killmail_hash), 'SRP Request has already been submitted');

        SrpRequest::create([
            'id' => $killmail_hash,
            'user_id' => auth()->user()->getAuthIdentifier(),
            'description' => $request->get('description') ?? ''
        ]);

        if(Killmail::firstWhere('killmail_hash', $killmail_hash))
            return redirect()->route('view.srp.request', $killmail_hash);

        if(cache($killmail_hash))
            return inertia('Srp/Processing', [
                'id' => $killmail_hash,
                'batchId' => cache($killmail_hash)
            ]);

        $batch = $this->dispatchBatch($killmail_id, $killmail_hash);

        return inertia('Srp/Processing', [
            'id' => $killmail_hash,
            'batchId' => $batch->id
        ]);
    }

    public function viewRequest(string $id)
    {

        $srp_request = SrpRequest::find($id);
        throw_unless($srp_request, 'unknown srp request id');

        $killmail = Killmail::query()
            ->with('ship', 'system', 'attackers')
            ->firstWhere('killmail_hash', $id);

        if($killmail->complete)
            return inertia('Srp/Killmail', [
                'killmail' => $killmail,
                'srpRequest' => $srp_request,
                'step' => $this->getStep($srp_request),
                'canEdit' => $this->canEdit($srp_request)
            ]);

        if(cache($id))
            return inertia('Srp/Processing', [
                'batchId' => cache($id)
            ]);

        $batch =  $this->dispatchBatch($killmail->killmail_id, $killmail->killmail_hash);

        return inertia('Srp/Processing', [
            'batchId' => $batch->id
        ]);

    }

    public function submitRequest(string $id) {

        request()->validate([
            'reimbursement' => ['required', 'numeric'],
        ]);

        $srp_request = SrpRequest::find($id);
        throw_unless($srp_request, 'unknown srp request id');

        throw_unless($srp_request->user_id === auth()->user()->getAuthIdentifier() || auth()->user()->can('accept or reject srp requests'),
            'you are not allowed to submit this request');

        $srp_request->status = 'submitted';
        $srp_request->reimbursement = request()->get('reimbursement');
        $srp_request->save();

        return redirect()->route('srp.request');

    }

    public function handleRequest(string $id, Request $request)
    {
        $request->validate([
            'sum' => ['required', 'numeric'],
            'killmail_hash' => ['required', 'string', 'exists:srp_requests,id'],
            'decision' => ['required', 'bool']
        ]);

        $srp_request = SrpRequest::find($id);
        $srp_request->status = $request->get('decision') ? 'accepted' : 'denied';
        $srp_request->reimbursement = $request->get('sum');
        $srp_request->save();

        if($srp_request->user_id === auth()->user()->getAuthIdentifier())
            return redirect()->route('review.srp.requests');

        return redirect()->route('srp.request');
    }

    private function getStep(SrpRequest $killmail)
    {
        return match ($killmail->status) {
            'open' => 1,
            'submitted' => 2,
            'accepted', 'denied', 'closed' => 3
        };
    }

    private function canEdit(SrpRequest $killmail)
    {
        return match ($killmail->status) {
            'open' => auth()->user()->getAuthIdentifier() === $killmail->user_id || auth()->user()->can('accept or reject srp requests'),
            'submitted' => auth()->user()->can('accept or reject srp requests'),
            'accepted', 'denied', 'closed' => false
        };
    }

    private function dispatchBatch(int $killmail_id, string $killmail_hash)
    {
        $batch = Bus::batch(
            [
                new KillmailJob($killmail_id, $killmail_hash)
            ])
            ->catch(fn($e) => logger()->debug($e))
            ->then(fn (Batch $batch) => cache()->forget($killmail_hash))
            ->name('Process Killmail Job')
            ->allowFailures()
            ->onQueue('high')->dispatch();

        Cache::forever($killmail_hash, $batch->id);

        return $batch;
    }
}