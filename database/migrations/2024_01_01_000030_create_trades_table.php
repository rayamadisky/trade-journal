<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the trades table.
 *
 * Core trade journal entries. Each trade is bound to a daily_ritual,
 * enforcing the Pre-Market Ritual requirement.
 * Uses JSONB for flexible tagging (Liquidity Sweep, Imbalance, News, etc.)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('ritual_id');
            $table->string('pair', 20)->comment('e.g. XAUUSD, SPX, EURUSD');
            $table->string('direction', 10)->comment('Long or Short');
            $table->decimal('entry_price', 16, 5);
            $table->decimal('stop_loss', 16, 5);
            $table->decimal('take_profit', 16, 5);
            $table->decimal('exit_price', 16, 5)->nullable();
            $table->decimal('lot_size', 10, 2);
            $table->decimal('pnl', 16, 2)->nullable()->comment('Profit/Loss in $, null while floating');
            $table->jsonb('tags')->default('[]')->comment('e.g. ["Liquidity Sweep", "Imbalance"]');
            $table->string('screenshot_entry')->nullable()->comment('Supabase Storage URL');
            $table->string('screenshot_exit')->nullable()->comment('Supabase Storage URL');
            $table->text('trade_notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('profiles')
                  ->onDelete('cascade');

            $table->foreign('ritual_id')
                  ->references('id')
                  ->on('daily_rituals')
                  ->onDelete('cascade');

            $table->index('user_id');
            $table->index('ritual_id');
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
