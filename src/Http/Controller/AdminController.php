<?php


namespace Seatplus\Srp\Http\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Seatplus\Srp\Models\SrpReceipt;
use Seatplus\Srp\Models\SrpRequest;

class AdminController extends Controller
{
    public function review()
    {
        $requests = SrpRequest::query()
            ->with('killmail', 'killmail.ship', 'killmail.system')
            ->where('status', 'submitted')
            ->orderByDesc('created_at')
            ->paginate();

        return inertia('Srp/Admin/Review', [
            'requests' => $requests,
            'tabs' => $this->getTabs()
        ]);
    }

    public function payout()
    {

        return inertia('Srp/Admin/Payout', [
            'tabs' => $this->getTabs()
        ]);
    }

    public function payoutData()
    {
        return SrpRequest::query()
            ->with('killmail', 'killmail.ship', 'killmail.system.region', 'user.main_character')
            ->where('status', 'accepted')
            ->oldest()
            ->paginate();
    }

    public function processPayout(Request $request)
    {
        $request->validate([
            'receiving_user_id' => ['required', 'numeric', 'exists:users,id'],
            'ids' => ['required', 'array', 'exists:srp_requests,id'],
            'amount' => ['required', 'numeric']
        ]);

        $receipt = SrpReceipt::create([
            'receiver_id' => $request->get('receiving_user_id'),
            'accountant_id' => auth()->user()->getAuthIdentifier(),
            'uuid' => Str::orderedUuid(),
            'amount' => $request->get('amount')
        ]);

        SrpRequest::query()
            ->whereIn('id', $request->get('ids'))
            ->update([
                'status' => 'closed',
                'receipt_id' => $receipt->id
            ]);

        return $receipt->uuid;
    }

    public function receipts()
    {
        $requests = SrpReceipt::query()
            ->with(['srp_requests' => fn($query) => $query->with('killmail', 'killmail.ship', 'killmail.system'), 'accountant.main_character', 'receiver.main_character'])
            ->paginate();

        return inertia('Srp/Admin/Receipts', [
            'requests' => $requests,
            'tabs' => $this->getTabs()
        ]);
    }

    private function getTabs()
    {
        $tabs = [
            [
                'name' => 'Review',
                'href' => 'review.srp.requests',
                'current' => false,
                'count' => SrpRequest::query()->where('status', 'submitted')->count()
            ],
            [
                'name' => 'Payout',
                'href' => 'payout.srp.requests',
                'current' => false,
                'count' => SrpRequest::query()->where('status', 'accepted')->get()->groupBy('user_id')->count()
            ],
            [
                'name' => 'Receipts',
                'href' => 'receipts.srp.requests',
                'current' => false,
                'count' => 0
            ]
        ];

        return collect($tabs)
            ->map(function ($tab){
                $tab['current'] = $tab['href'] === request()->route()->action['as'];
                $tab['href'] = route($tab['href']);

                return $tab;
            })
            ->toArray();

        return $tabs;
    }

}