<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trading_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name'); // e.g., 'Main', 'Prop Firm', 'Cent'
            $table->string('type')->default('Real'); // e.g., Real, Demo, Prop
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->timestamps();
        });

        Schema::table('trading_accounts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_accounts');
    }
};
