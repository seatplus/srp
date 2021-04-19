<?php


namespace Seatplus\Srp\Http\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Seatplus\Srp\Models\SrpRequest;

class RequestController extends Controller
{
    public function index()
    {
        return inertia('Srp/Request');
    }

    public function store(Request $request)
    {
        $request->validate([
            'killmailUrl' => ['required', 'string']
        ]);

        $response = Http::get($request->get('killmailUrl'));

        if($response->ok()) {
            $killmail = $response->body();

            $srp_request = SrpRequest::create([
                'id' => md5($killmail),
                'user_id' => auth()->user()->getAuthIdentifier(),
                'killmail' => $killmail,
                'description' => $request->get('description') ?? ''
            ]);
        }

        return redirect()->route('view.srp.request', $srp_request->id);
    }

    public function viewRequest(string $id)
    {
        dd(SrpRequest::find($id)->killmail);
    }
}