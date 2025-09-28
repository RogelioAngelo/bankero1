<?php
// Lists transactions that appear paid/approved but have no order_id
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;

$txs = Transaction::whereIn('status', ['approved','paid'])
    ->whereNull('order_id')
    ->orderBy('updated_at', 'desc')
    ->get();

if ($txs->isEmpty()) {
    echo "No approved transactions without order_id found.\n";
    exit(0);
}

foreach ($txs as $t) {
    echo "id={$t->id} user_id={$t->user_id} checkout_id={$t->checkout_id} status={$t->status} updated_at={$t->updated_at}\n";
    echo "meta: " . json_encode($t->meta) . "\n\n";
}
