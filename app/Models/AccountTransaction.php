<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AccountTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'account_id',
        'type', // deposit, withdrawal
        'amount',
        'notes',
    ];

    public function account()
    {
        return $this->belongsTo(TradingAccount::class);
    }
}
