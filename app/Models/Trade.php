<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trade model - the core trade journal entry.
 *
 * Each trade is bound to a daily_ritual, enforcing the rule
 * that no trades can be logged without completing the ritual first.
 */
class Trade extends Model
{
    use HasUuids;

    protected $table = 'trades';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'account_id',
        'ritual_id',
        'pair',
        'direction',
        'entry_price',
        'stop_loss',
        'take_profit',
        'exit_price',
        'lot_size',
        'pnl',
        'tags',
        'screenshot_entry',
        'screenshot_exit',
        'trade_notes',
    ];

    protected $casts = [
        'entry_price' => 'decimal:5',
        'stop_loss' => 'decimal:5',
        'take_profit' => 'decimal:5',
        'exit_price' => 'decimal:5',
        'lot_size' => 'decimal:2',
        'pnl' => 'decimal:2',
        'tags' => 'array',
    ];

    /**
     * Get the profile that owns this trade.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'user_id', 'id');
    }

    /**
     * Get the account this trade belongs to.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(TradingAccount::class, 'account_id');
    }

    /**
     * Get the daily ritual this trade is linked to.
     */
    public function ritual(): BelongsTo
    {
        return $this->belongsTo(DailyRitual::class, 'ritual_id', 'id');
    }

    /**
     * Check if this trade is still open (no exit price set).
     */
    public function isOpen(): bool
    {
        return is_null($this->exit_price);
    }

    /**
     * Check if this trade is a winner.
     */
    public function isWinner(): bool
    {
        return !is_null($this->pnl) && $this->pnl > 0;
    }
}
