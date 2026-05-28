<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Profile model - stores user profile data and gamification scores.
 *
 * Linked to Supabase auth.users via user_id (UUID).
 */
class Profile extends Model
{
    use HasUuids;

    protected $table = 'profiles';

    protected $fillable = [
        'user_id',
        'username',
        'discipline_score',
        'default_max_loss',
    ];

    protected $casts = [
        'discipline_score' => 'integer',
        'default_max_loss' => 'decimal:2',
    ];

    /**
     * Get all daily rituals for this profile.
     */
    public function dailyRituals(): HasMany
    {
        return $this->hasMany(DailyRitual::class, 'user_id', 'id');
    }

    /**
     * Get all trades for this profile.
     */
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class, 'user_id');
    }

    /**
     * Get all trading accounts belonging to the user.
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(TradingAccount::class, 'user_id');
    }
}
