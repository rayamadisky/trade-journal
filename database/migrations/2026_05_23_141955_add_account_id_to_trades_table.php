<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->uuid('account_id')->nullable()->after('user_id');
            $table->foreign('account_id')->references('id')->on('trading_accounts')->onDelete('cascade');
        });

        // Data Migration: Create a default account for each profile and assign trades
        $profiles = DB::table('profiles')->get();
        foreach ($profiles as $profile) {
            $accountId = (string) Str::uuid();
            DB::table('trading_accounts')->insert([
                'id' => $accountId,
                'user_id' => $profile->id,
                'name' => 'Main Account',
                'type' => 'Real',
                'balance' => 0,
                'currency' => 'USD',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign all existing trades to this new account
            DB::table('trades')->where('user_id', $profile->id)->update(['account_id' => $accountId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
            $table->dropColumn('account_id');
        });
    }
};
