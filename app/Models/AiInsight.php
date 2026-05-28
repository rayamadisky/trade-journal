<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AiInsight extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'context_data',
        'insight_text',
    ];

    protected $casts = [
        'context_data' => 'array',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'user_id');
    }
}
