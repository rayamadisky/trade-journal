<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TradingPair extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'symbol'];

    public function user()
    {
        return $this->belongsTo(Profile::class, 'user_id');
    }
}
