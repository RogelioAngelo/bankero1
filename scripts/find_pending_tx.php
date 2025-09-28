<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
$tx = Transaction::where('mode','e-wallet')->where('status','pending')->latest()->first();
if (!$tx) {
    echo "NO_PENDING_TX\n";
    exit(0);
}
print_r(['id' => $tx->id, 'checkout_id' => $tx->checkout_id, 'meta' => $tx->meta]);
