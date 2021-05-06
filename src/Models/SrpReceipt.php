<?php


namespace Seatplus\Srp\Models;


use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Killmails\Killmail;
use Seatplus\Srp\database\factories\SrpRequestFactory;

class SrpReceipt extends Model
{

    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected static function newFactory()
    {
        return; //TODO SrpRequestFactory::new();
    }

    public function srp_requests()
    {
        return $this->hasMany(SrpRequest::class, 'receipt_id', 'id');
    }

    public function accountant()
    {
        return $this->belongsTo(User::class, 'accountant_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }


}