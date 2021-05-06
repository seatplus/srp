<?php


namespace Seatplus\Srp\Http\Controller;


use Seatplus\Srp\Models\SrpReceipt;

class ReceiptController
{
    public function index(string $uuid)
    {
        return inertia('Srp/Receipt/Index', [
            'receipt' => SrpReceipt::with(['srp_requests' => fn($query) => $query->with('killmail', 'killmail.ship', 'killmail.system'), 'accountant.main_character', 'receiver.main_character'])
                ->firstWhere('uuid', $uuid)
        ]);
    }

}