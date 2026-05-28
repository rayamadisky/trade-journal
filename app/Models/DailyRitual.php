<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * DailyRitual model - the "Pre-Market & Post-Market Check-in".
 *
 * This is the heart of TradeRitual: users must complete this
 * before they can access the trade entry form.
 */
class DailyRitual extends Model
{
    use HasUuids;

    protected $table = 'daily_rituals';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'date',
        'sleep_hours',
        'pre_mood',
        'max_loss_limit',
        'post_mood',
        'followed_plan',
        'daily_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'sleep_hours' => 'integer',
        'pre_mood' => 'integer',
        'max_loss_limit' => 'decimal:2',
        'post_mood' => 'integer',
        'followed_plan' => 'boolean',
    ];

    /**
     * Get the profile that owns this ritual.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'user_id', 'id');
    }

    /**
     * Get all trades logged during this ritual day.
     */
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'ritual_id', 'id');
    }
}
