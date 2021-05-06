<?php

namespace Seatplus\Srp\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Killmails\Killmail;
use Seatplus\Srp\database\factories\SrpRequestFactory;

class SrpRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected static function newFactory()
    {
        return SrpRequestFactory::new();
    }

    public function killmail()
    {
        return $this->belongsTo(Killmail::class, 'id', 'killmail_hash');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
