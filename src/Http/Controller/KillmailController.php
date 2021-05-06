<?php


namespace Seatplus\Srp\Http\Controller;


use Inertia\Inertia;
use Seatplus\Eveapi\Models\Killmails\KillmailItem;

class KillmailController extends Controller
{
    public function items(int $location_id)
    {
        $query = KillmailItem::query()
            ->with('type')
            ->where('location_id', $location_id);

        return $query->paginate();
    }

    public function updateItem(int $item_id)
    {
        request()->validate([
            'price' => ['required', 'numeric']
        ]);

        KillmailItem::where('id', $item_id)->update(['price' => request()->get('price')]);
    }

}