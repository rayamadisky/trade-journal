<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the daily_rituals table.
 *
 * The heart of TradeRitual - records pre-market and post-market check-ins.
 * A user must have a ritual for the current date before logging trades.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_rituals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->date('date');
            $table->integer('sleep_hours');
            $table->integer('pre_mood')->comment('Scale 1-5');
            $table->decimal('max_loss_limit', 12, 2);
            $table->integer('post_mood')->nullable()->comment('Scale 1-5, filled post-market');
            $table->boolean('followed_plan')->nullable()->comment('Filled post-market');
            $table->text('daily_notes')->nullable();

            // Ensure one ritual per user per day
            $table->unique(['user_id', 'date']);

            $table->foreign('user_id')
                  ->references('id')
                  ->on('profiles')
                  ->onDelete('cascade');

            $table->index(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_rituals');
    }
};
