<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TradingAccount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'balance',
        'currency',
    ];

    public function user()
    {
        return $this->belongsTo(Profile::class, 'user_id');
    }

    public function trades()
    {
        return $this->hasMany(Trade::class, 'account_id');
    }

    public function transactions()
    {
        return $this->hasMany(AccountTransaction::class, 'account_id');
    }
}
