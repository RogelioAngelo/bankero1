<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = App\Models\Transaction::orderBy('updated_at', 'desc')
    ->take(50)->get(['id','checkout_id','amount','status','order_id','meta','created_at','updated_at']);

$out = [];
foreach ($rows as $r) {
    $meta = $r->meta;
    if (! is_array($meta) && $meta) $meta = (array) json_decode((string) $meta, true);
    $out[] = [
        'id' => $r->id,
        'checkout_id' => $r->checkout_id,
        'amount' => $r->amount,
        'status' => $r->status,
        'order_id' => $r->order_id,
        'created_at' => $r->created_at,
        'updated_at' => $r->updated_at,
        'meta_total' => $meta['totals']['total'] ?? null,
    ];
}
echo json_encode($out, JSON_PRETTY_PRINT) . PHP_EOL;

// Also show the last 10 orders created in last day
$orders = App\Models\Order::orderBy('created_at','desc')->take(10)->get(['id','user_id','total','payment_status','created_at']);
echo "\nRecent Orders:\n";
echo json_encode($orders->toArray(), JSON_PRETTY_PRINT) . PHP_EOL;
