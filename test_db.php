<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement('DROP TABLE IF EXISTS "trading_accounts" CASCADE');
    echo "Dropped trading_accounts\n";
    $rows = DB::select('SELECT * FROM trading_accounts');
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
